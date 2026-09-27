<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Etablissement;
use App\Models\Movement;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    /**
     * Récupérer les établissements de l'utilisateur
     */
    private function getUserEtablissements()
    {
        return Etablissement::whereHas('users', function ($q) {
            $q->where('users.id', auth()->id());
        })
        ->where('statut', 'actif')
        ->pluck('id');
    }

    /**
     * Point de vente - Nouvelle vente
     */
    public function pos(Request $request)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();
        
        if (!$etablissementIds->contains($selectedEtablissementId)) {
            $selectedEtablissementId = $etablissementIds->first();
        }

        // Récupérer les produits
        $products = Product::with(['stock', 'unit', 'category'])
            ->where('etablissement_id', $selectedEtablissementId)
            ->where('status', true)
            ->whereHas('stock', function($q) {
                $q->where('quantity', '>', 0);
            })
            ->orderBy('name')
            ->limit(20)
            ->get();

        $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();
        $etablissement = $etablissements->firstWhere('id', $selectedEtablissementId);

        // Récupérer les clients
        $customers = Customer::where('etablissement_id', $selectedEtablissementId)
            ->orderBy('name')
            ->get();

        // Paramètres TVA
        $tvaRates = [0, 5.5, 10, 20];

        return view('client.sales.pos', compact(
            'products',
            'etablissements',
            'etablissement',
            'selectedEtablissementId',
            'customers',
            'tvaRates'
        ));
    }

    /**
     * Recherche de produits pour le POS
     */
    public function searchProducts(Request $request)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return response()->json([
                'success' => false,
                'data' => []
            ]);
        }

        $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();

        $query = Product::with(['stock', 'unit', 'category'])
            ->where('etablissement_id', $selectedEtablissementId)
            ->where('status', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('code', 'LIKE', '%' . $search . '%')
                  ->orWhere('barcode', 'LIKE', '%' . $search . '%');
            });
        }

        $products = $query->whereHas('stock', function($q) {
                $q->where('quantity', '>', 0);
            })
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Enregistrer une vente avec TVA
     */
    public function store(Request $request)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Vous n\'avez aucun établissement actif.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'etablissement_id' => 'required|in:' . $etablissementIds->implode(','),
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.tva_rate' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,transfer,other',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'customer_address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cartItems = [];
        $totalHT = 0;
        $totalTVA = 0;
        $totalTTC = 0;

        // Vérifier les stocks et calculer les totaux
        foreach ($request->items as $item) {
            $product = Product::with(['stock'])
                ->where('etablissement_id', $request->etablissement_id)
                ->find($item['product_id']);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produit non trouvé'
                ], 422);
            }

            if (!$product->status) {
                return response()->json([
                    'success' => false,
                    'message' => $product->name . ' est désactivé'
                ], 422);
            }

            $stockQty = $product->stock->quantity ?? 0;
            if ($stockQty < $item['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuffisant pour ' . $product->name . ' (disponible: ' . $stockQty . ')'
                ], 422);
            }

            $subtotalHT = $item['quantity'] * $item['price'];
            $tvaAmount = $subtotalHT * ($item['tva_rate'] / 100);
            $subtotalTTC = $subtotalHT + $tvaAmount;

            $totalHT += $subtotalHT;
            $totalTVA += $tvaAmount;
            $totalTTC += $subtotalTTC;

            $cartItems[] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'price_ht' => $item['price'],
                'tva_rate' => $item['tva_rate'],
                'tva_amount' => $tvaAmount,
                'subtotal_ht' => $subtotalHT,
                'subtotal_ttc' => $subtotalTTC,
            ];
        }

        DB::beginTransaction();
        try {
            // Gérer le client
            $customerId = $request->customer_id;
            if (!$customerId && $request->customer_name) {
                $customer = Customer::create([
                    'etablissement_id' => $request->etablissement_id,
                    'name' => $request->customer_name,
                    'phone' => $request->customer_phone,
                    'email' => $request->customer_email,
                    'address' => $request->customer_address,
                    'user_id' => Auth::id(),
                ]);
                $customerId = $customer->id;
            }

            // Créer la commande
            $order = Order::create([
                'etablissement_id' => $request->etablissement_id,
                'user_id' => Auth::id(),
                'customer_id' => $customerId,
                'order_number' => 'POS-' . date('Ymd') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT),
                'customer_name' => $request->customer_name ?? 'Client physique',
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'customer_address' => $request->customer_address,
                'notes' => $request->notes,
                'total_ht' => $totalHT,
                'total_tva' => $totalTVA,
                'total_amount' => $totalTTC,
                'status' => 'completed',
                'payment_method' => $request->payment_method,
                'payment_status' => 'paid',
                'order_date' => now(),
                'type' => 'pos',
            ]);

            // Créer les items et mettre à jour les stocks
            foreach ($cartItems as $item) {
                $product = $item['product'];
                $quantity = $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price_ht' => $item['price_ht'],
                    'tva_rate' => $item['tva_rate'],
                    'tva_amount' => $item['tva_amount'],
                    'subtotal_ht' => $item['subtotal_ht'],
                    'subtotal_ttc' => $item['subtotal_ttc'],
                ]);

                // Mettre à jour le stock
                $stock = Stock::where('product_id', $product->id)
                    ->where('etablissement_id', $request->etablissement_id)
                    ->first();

                if ($stock) {
                    $oldQuantity = $stock->quantity;
                    $stock->quantity -= $quantity;
                    $stock->save();

                    Movement::create([
                        'etablissement_id' => $request->etablissement_id,
                        'product_id' => $product->id,
                        'type' => 'out',
                        'quantity' => $quantity,
                        'before' => $oldQuantity,
                        'after' => $stock->quantity,
                        'purchase_price' => $stock->purchase_price,
                        'selling_price' => $item['price_ht'],
                        'note' => 'Vente POS #' . $order->order_number,
                        'user_id' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vente enregistrée avec succès !',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total_ht' => $totalHT,
                'total_tva' => $totalTVA,
                'total_ttc' => $totalTTC,
                'invoice_url' => route('client.sales.invoice', $order->id)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Facture PDF
     */
    public function invoice($id)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            abort(403, 'Accès non autorisé');
        }

        $order = Order::with(['items', 'items.product', 'items.product.category', 'items.product.unit', 'user', 'customer', 'etablissement'])
            ->whereIn('etablissement_id', $etablissementIds)
            ->findOrFail($id);

        return view('client.sales.invoice', compact('order'));
    }

    /**
     * Générer la facture PDF
     */
    public function generateInvoice($id)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            abort(403, 'Accès non autorisé');
        }

        $order = Order::with(['items', 'items.product', 'items.product.category', 'items.product.unit', 'user', 'customer', 'etablissement'])
            ->whereIn('etablissement_id', $etablissementIds)
            ->findOrFail($id);

        $pdf = Pdf::loadView('client.sales.invoice-pdf', compact('order'));
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('facture-' . $order->order_number . '.pdf');
    }

    /**
     * Imprimer la facture
     */
    public function printInvoice($id)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            abort(403, 'Accès non autorisé');
        }

        $order = Order::with(['items', 'items.product', 'items.product.category', 'items.product.unit', 'user', 'customer', 'etablissement'])
            ->whereIn('etablissement_id', $etablissementIds)
            ->findOrFail($id);

        return view('client.sales.invoice-print', compact('order'));
    }

    /**
     * Historique des ventes
     */
    public function history(Request $request)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();

        $query = Order::with(['items', 'items.product', 'user', 'customer'])
            ->whereIn('etablissement_id', $etablissementIds)
            ->whereIn('type', ['pos', 'direct']);

        if ($request->filled('etablissement_id')) {
            $query->where('etablissement_id', $request->etablissement_id);
        }

        if ($request->filled('search')) {
            $query->where('order_number', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();

        return view('client.sales.history', compact(
            'orders',
            'etablissements',
            'selectedEtablissementId'
        ));
    }

    /**
     * Statistiques des ventes
     */
    public function stats(Request $request)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun établissement trouvé'
            ], 403);
        }

        $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();

        // Ventes du jour
        $todaySales = Order::where('etablissement_id', $selectedEtablissementId)
            ->whereDate('created_at', today())
            ->whereIn('type', ['pos', 'direct'])
            ->sum('total_amount');

        // Ventes de la semaine
        $weekSales = Order::where('etablissement_id', $selectedEtablissementId)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereIn('type', ['pos', 'direct'])
            ->sum('total_amount');

        // Ventes du mois
        $monthSales = Order::where('etablissement_id', $selectedEtablissementId)
            ->whereMonth('created_at', now()->month)
            ->whereIn('type', ['pos', 'direct'])
            ->sum('total_amount');

        // Top produits
        $topProducts = OrderItem::whereHas('order', function($q) use ($selectedEtablissementId) {
                $q->where('etablissement_id', $selectedEtablissementId)
                  ->whereIn('type', ['pos', 'direct']);
            })
            ->with('product')
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'today_sales' => $todaySales,
                'week_sales' => $weekSales,
                'month_sales' => $monthSales,
                'top_products' => $topProducts
            ]
        ]);
    }
}