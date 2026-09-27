<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Movement;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ClientStockController extends Controller
{
    /**
     * Récupérer les établissements de l'utilisateur connecté
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
     * Tableau de bord client
     */
    public function dashboards(Request $request)
    {
        // Récupérer tous les établissements de l'utilisateur
        $etablissements = Etablissement::whereHas('users', function ($q) {
            $q->where('users.id', auth()->id());
        })
        ->where('statut', 'actif')
        ->get();

        if ($etablissements->isEmpty()) {
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        $etablissementIds = $etablissements->pluck('id');
        $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();

        if (!$etablissementIds->contains($selectedEtablissementId)) {
            $selectedEtablissementId = $etablissementIds->first();
        }

        $etablissement = $etablissements->firstWhere('id', $selectedEtablissementId);

        // ===== STATISTIQUES =====
        $stats = [
            'total_products' => Stock::where('etablissement_id', $selectedEtablissementId)->sum('quantity'),
            'total_value' => Stock::where('etablissement_id', $selectedEtablissementId)
                ->sum(DB::raw('quantity * purchase_price')),
            'total_selling_value' => Stock::where('etablissement_id', $selectedEtablissementId)
                ->sum(DB::raw('quantity * selling_price')),
            'potential_profit' => Stock::where('etablissement_id', $selectedEtablissementId)
                ->sum(DB::raw('quantity * (selling_price - purchase_price)')),
            'product_count' => Stock::where('etablissement_id', $selectedEtablissementId)->count(),
            'low_stock_count' => Stock::where('etablissement_id', $selectedEtablissementId)
                ->whereRaw('quantity <= minimum_stock')->count(),
            'out_of_stock_count' => Stock::where('etablissement_id', $selectedEtablissementId)
                ->where('quantity', '<=', 0)->count(),
        ];

        // ===== STOCK PAR PRODUIT =====
        $stockByProduct = Stock::with(['product', 'product.category', 'product.unit'])
            ->where('etablissement_id', $selectedEtablissementId)
            ->orderBy('quantity', 'desc')
            ->get();

        // ===== DERNIERS MOUVEMENTS =====
        $recentMovements = Movement::with(['product', 'user'])
            ->where('etablissement_id', $selectedEtablissementId)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // ===== PRODUITS EN RUPTURE =====
        $outOfStock = Stock::with(['product', 'product.category'])
            ->where('etablissement_id', $selectedEtablissementId)
            ->where('quantity', '<=', 0)
            ->get();

        // ===== PRODUITS EN STOCK BAS =====
        $lowStock = Stock::with(['product', 'product.category'])
            ->where('etablissement_id', $selectedEtablissementId)
            ->whereRaw('quantity <= minimum_stock')
            ->where('quantity', '>', 0)
            ->get();

        // ===== ALERTES =====
        $alerts = collect();
        foreach ($lowStock as $item) {
            $alerts->push([
                'product' => $item->product->name,
                'quantity' => $item->quantity,
                'minimum' => $item->minimum_stock,
                'percentage' => round(($item->quantity / $item->minimum_stock) * 100),
                'type' => 'low_stock',
                'message' => "Stock bas pour {$item->product->name} ({$item->quantity} restants)"
            ]);
        }
        foreach ($outOfStock as $item) {
            $alerts->push([
                'product' => $item->product->name,
                'quantity' => 0,
                'minimum' => $item->minimum_stock,
                'percentage' => 0,
                'type' => 'out_of_stock',
                'message' => "Rupture de stock pour {$item->product->name}"
            ]);
        }
        $alerts = $alerts->sortBy('percentage')->take(10);

        // ===== VALORISATION DU STOCK (corrigé) =====
        $stockValuation = Stock::where('etablissement_id', $selectedEtablissementId)
            ->select(
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(quantity * purchase_price) as total_purchase_value'),
                DB::raw('SUM(quantity * selling_price) as total_selling_value'),
                DB::raw('SUM(quantity * (selling_price - purchase_price)) as total_profit')
            )
            ->first();

        // ===== VALORISATION PAR CATÉGORIE (corrigé) =====
        $valuationByCategory = Stock::where('stocks.etablissement_id', $selectedEtablissementId)
            ->join('products', 'stocks.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select(
                'categories.id',
                'categories.nom as category_name',
                DB::raw('SUM(stocks.quantity) as total_quantity'),
                DB::raw('SUM(stocks.quantity * stocks.purchase_price) as total_value')
            )
            ->groupBy('categories.id', 'categories.nom')
            ->orderBy('total_value', 'desc')
            ->get();

        return view('client.dashboard', compact(
            'etablissements',
            'etablissement',
            'selectedEtablissementId',
            'stats',
            'stockByProduct',
            'recentMovements',
            'outOfStock',
            'lowStock',
            'alerts',
            'stockValuation',
            'valuationByCategory'
        ));
    }

    /**
     * Liste des stocks
     */
    public function index(Request $request)
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

        $query = Stock::with(['product', 'product.category', 'product.unit'])
            ->where('etablissement_id', $selectedEtablissementId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('code', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->whereHas('product', function($q) use ($request) {
                $q->where('category_id', $request->category_id);
            });
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereRaw('quantity <= minimum_stock')->where('quantity', '>', 0);
            } elseif ($request->stock_status === 'out') {
                $query->where('quantity', '<=', 0);
            } elseif ($request->stock_status === 'normal') {
                $query->whereRaw('quantity > minimum_stock');
            }
        }

        $stocks = $query->orderBy('quantity', 'asc')->paginate(20);

        $categories = \App\Models\Category::where('status', true)->get();
        $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();

        return view('client.stocks.index', compact(
            'stocks', 
            'categories', 
            'etablissements', 
            'selectedEtablissementId'
        ));
    }

    /**
     * Entrée de stock
     */
    public function stockIn(Request $request)
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

        if ($request->isMethod('post')) {
            $request->validate([
                'etablissement_id' => 'required|in:' . $etablissementIds->implode(','),
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'purchase_price' => 'required|numeric|min:0',
                'selling_price' => 'required|numeric|min:0',
                'note' => 'nullable|string|max:500',
            ]);

            $stock = Stock::where('etablissement_id', $request->etablissement_id)
                ->where('product_id', $request->product_id)
                ->first();

            DB::beginTransaction();
            try {
                if ($stock) {
                    $oldQuantity = $stock->quantity;
                    $oldPurchasePrice = $stock->purchase_price;

                    $totalCost = ($oldQuantity * $oldPurchasePrice) + ($request->quantity * $request->purchase_price);
                    $newQuantity = $oldQuantity + $request->quantity;
                    $newPurchasePrice = $totalCost / $newQuantity;

                    $stock->quantity = $newQuantity;
                    $stock->purchase_price = $newPurchasePrice;
                    $stock->selling_price = $request->selling_price;
                    $stock->save();

                    $movementType = 'stock_in_update';
                } else {
                    $stock = Stock::create([
                        'etablissement_id' => $request->etablissement_id,
                        'product_id' => $request->product_id,
                        'quantity' => $request->quantity,
                        'purchase_price' => $request->purchase_price,
                        'selling_price' => $request->selling_price,
                        'minimum_stock' => 0,
                    ]);

                    $oldQuantity = 0;
                    $movementType = 'stock_in_create';
                }

                Movement::create([
                    'etablissement_id' => $request->etablissement_id,
                    'product_id' => $request->product_id,
                    'type' => 'in',
                    'quantity' => $request->quantity,
                    'before' => $oldQuantity,
                    'after' => $stock->quantity,
                    'purchase_price' => $request->purchase_price,
                    'selling_price' => $request->selling_price,
                    'note' => $request->note ?? 'Approvisionnement',
                    'user_id' => Auth::id(),
                ]);

                DB::commit();

                return redirect()->route('client.stocks.index', ['etablissement_id' => $request->etablissement_id])
                    ->with('success', 'Stock ajouté avec succès !');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Erreur: ' . $e->getMessage());
            }
        }

        $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();
        $etablissement = $etablissements->firstWhere('id', $selectedEtablissementId);

        $products = Product::where('status', true)
            ->whereDoesntHave('stock', function($q) use ($selectedEtablissementId) {
                $q->where('etablissement_id', $selectedEtablissementId);
            })
            ->get();

        $existingProducts = Stock::where('etablissement_id', $selectedEtablissementId)
            ->with('product')
            ->get();

        return view('client.stocks.stock-in', compact(
            'etablissement',
            'etablissements',
            'selectedEtablissementId',
            'products',
            'existingProducts'
        ));
    }

    /**
     * Sortie de stock
     */
    public function stockOut(Request $request)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();

        if ($request->isMethod('post')) {
            $request->validate([
                'stock_id' => 'required|exists:stocks,id',
                'quantity' => 'required|integer|min:1',
                'note' => 'nullable|string|max:500',
            ]);

            $stock = Stock::where('etablissement_id', $selectedEtablissementId)
                ->where('id', $request->stock_id)
                ->firstOrFail();

            if ($stock->quantity < $request->quantity) {
                return back()->with('error', 'Quantité insuffisante en stock.');
            }

            DB::beginTransaction();
            try {
                $oldQuantity = $stock->quantity;
                $stock->quantity -= $request->quantity;
                $stock->save();

                Movement::create([
                    'etablissement_id' => $selectedEtablissementId,
                    'product_id' => $stock->product_id,
                    'type' => 'out',
                    'quantity' => $request->quantity,
                    'before' => $oldQuantity,
                    'after' => $stock->quantity,
                    'purchase_price' => $stock->purchase_price,
                    'selling_price' => $stock->selling_price,
                    'note' => $request->note ?? 'Sortie de stock',
                    'user_id' => Auth::id(),
                ]);

                DB::commit();

                return redirect()->route('client.stocks.index', ['etablissement_id' => $selectedEtablissementId])
                    ->with('success', 'Sortie de stock effectuée avec succès !');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Erreur: ' . $e->getMessage());
            }

        }

        $stocks = Stock::where('etablissement_id', $selectedEtablissementId)
            ->with('product')
            ->where('quantity', '>', 0)
            ->orderBy('product_id')
            ->get();

        $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();
        $etablissement = $etablissements->firstWhere('id', $selectedEtablissementId);

        return view('client.stocks.stock-out', compact(
            'etablissement',
            'etablissements',
            'selectedEtablissementId',
            'stocks'
        ));
    }
/**
 * Produits en rupture
 */
public function outOfStock(Request $request)
{
    $etablissementIds = $this->getUserEtablissements();
    
    if ($etablissementIds->isEmpty()) {
        return redirect()->route('client.profile.edit')
            ->with('error', 'Vous n\'avez aucun établissement actif.');
    }

    $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();

    $stocks = Stock::where('etablissement_id', $selectedEtablissementId)
        ->with(['product', 'product.category', 'product.unit'])
        ->where('quantity', '<=', 0)
        ->orderBy('product_id')
        ->paginate(20);

    // Statistiques
    $totalProducts = Stock::where('etablissement_id', $selectedEtablissementId)->count();
    $lowStockCount = Stock::where('etablissement_id', $selectedEtablissementId)
        ->whereRaw('quantity <= minimum_stock')
        ->where('quantity', '>', 0)
        ->count();
    $normalStockCount = Stock::where('etablissement_id', $selectedEtablissementId)
        ->whereRaw('quantity > minimum_stock')
        ->where('quantity', '>', 0)
        ->count();

    $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();

    return view('client.stocks.out-of-stock', compact(
        'stocks',
        'etablissements',
        'selectedEtablissementId',
        'totalProducts',
        'lowStockCount',
        'normalStockCount'
    ));
}
    /**
     * Ajustement de stock
     */
    public function stockAdjust(Request $request)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();

        if ($request->isMethod('post')) {
            $request->validate([
                'stock_id' => 'required|exists:stocks,id',
                'new_quantity' => 'required|integer|min:0',
                'reason' => 'required|string|max:500',
            ]);

            $stock = Stock::where('etablissement_id', $selectedEtablissementId)
                ->where('id', $request->stock_id)
                ->firstOrFail();

            DB::beginTransaction();
            try {
                $oldQuantity = $stock->quantity;
                $difference = $request->new_quantity - $oldQuantity;
                $stock->quantity = $request->new_quantity;
                $stock->save();

                Movement::create([
                    'etablissement_id' => $selectedEtablissementId,
                    'product_id' => $stock->product_id,
                    'type' => $difference > 0 ? 'adjust_positive' : 'adjust_negative',
                    'quantity' => abs($difference),
                    'before' => $oldQuantity,
                    'after' => $stock->quantity,
                    'purchase_price' => $stock->purchase_price,
                    'selling_price' => $stock->selling_price,
                    'note' => 'Ajustement: ' . $request->reason,
                    'user_id' => Auth::id(),
                ]);

                DB::commit();

                return redirect()->route('client.stocks.index', ['etablissement_id' => $selectedEtablissementId])
                    ->with('success', 'Stock ajusté avec succès !');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Erreur: ' . $e->getMessage());
            }
        }

        $stocks = Stock::where('etablissement_id', $selectedEtablissementId)
            ->with('product')
            ->orderBy('product_id')
            ->get();

        $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();
        $etablissement = $etablissements->firstWhere('id', $selectedEtablissementId);

        return view('client.stocks.stock-adjust', compact(
            'etablissement',
            'etablissements',
            'selectedEtablissementId',
            'stocks'
        ));
    }

    /**
     * Historique des mouvements
     */
    // public function movements(Request $request)
    // {
    //     $etablissementIds = $this->getUserEtablissements();
        
    //     if ($etablissementIds->isEmpty()) {
    //         return redirect()->route('client.profile.edit')
    //             ->with('error', 'Vous n\'avez aucun établissement actif.');
    //     }

    //     $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();

    //     $query = Movement::where('etablissement_id', $selectedEtablissementId)
    //         ->with(['product', 'user']);

    //     if ($request->filled('type')) {
    //         $query->where('type', $request->type);
    //     }

    //     if ($request->filled('product_id')) {
    //         $query->where('product_id', $request->product_id);
    //     }

    //     if ($request->filled('date_from')) {
    //         $query->whereDate('created_at', '>=', $request->date_from);
    //     }

    //     if ($request->filled('date_to')) {
    //         $query->whereDate('created_at', '<=', $request->date_to);
    //     }

    //     $movements = $query->orderBy('created_at', 'desc')->paginate(30);

    //     $products = Product::whereHas('stock', function($q) use ($selectedEtablissementId) {
    //         $q->where('etablissement_id', $selectedEtablissementId);
    //     })->get();

    //     $types = [
    //         'in' => 'Entrée',
    //         'out' => 'Sortie',
    //         'adjust_positive' => 'Ajustement +',
    //         'adjust_negative' => 'Ajustement -',
    //         'inventory_in' => 'Inventaire +',
    //         'inventory_out' => 'Inventaire -',
    //         'stock_in_create' => 'Création stock',
    //         'stock_in_update' => 'Approvisionnement',
    //     ];

    //     $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();
    //     $etablissement = $etablissements->firstWhere('id', $selectedEtablissementId);

    //     return view('client.stocks.movements', compact(
    //         'etablissement',
    //         'etablissements',
    //         'selectedEtablissementId',
    //         'movements',
    //         'products',
    //         'types'
    //     ));
    // }

    /**
 * Historique des mouvements
 */
public function movements(Request $request)
{
    $etablissementIds = $this->getUserEtablissements();
    
    if ($etablissementIds->isEmpty()) {
        return redirect()->route('client.profile.edit')
            ->with('error', 'Vous n\'avez aucun établissement actif.');
    }

    $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();

    $query = Movement::where('etablissement_id', $selectedEtablissementId)
        ->with(['product', 'user']);

    // Filtres
    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

    if ($request->filled('product_id')) {
        $query->where('product_id', $request->product_id);
    }

    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    $movements = $query->orderBy('created_at', 'desc')->paginate(30);

    // Statistiques
    $totalIn = Movement::where('etablissement_id', $selectedEtablissementId)
        ->where('type', 'in')->count();
    $totalOut = Movement::where('etablissement_id', $selectedEtablissementId)
        ->where('type', 'out')->count();
    $totalAdjust = Movement::where('etablissement_id', $selectedEtablissementId)
        ->whereIn('type', ['adjust_positive', 'adjust_negative'])->count();

    // Produits pour le filtre
    $products = Product::whereHas('stock', function($q) use ($selectedEtablissementId) {
        $q->where('etablissement_id', $selectedEtablissementId);
    })->get();

    $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();

    return view('client.stocks.movements', compact(
        'movements',
        'products',
        'etablissements',
        'selectedEtablissementId',
        'totalIn',
        'totalOut',
        'totalAdjust'
    ));
}

    /**
     * Alertes de stock minimum
     */
    // public function alerts(Request $request)
    // {
    //     $etablissementIds = $this->getUserEtablissements();
        
    //     if ($etablissementIds->isEmpty()) {
    //         return redirect()->route('client.profile.edit')
    //             ->with('error', 'Vous n\'avez aucun établissement actif.');
    //     }

    //     $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();

    //     $lowStock = Stock::where('etablissement_id', $selectedEtablissementId)
    //         ->with(['product', 'product.category'])
    //         ->whereRaw('quantity <= minimum_stock')
    //         ->where('quantity', '>', 0)
    //         ->orderBy('quantity', 'asc')
    //         ->paginate(20);

    //     $outOfStock = Stock::where('etablissement_id', $selectedEtablissementId)
    //         ->with(['product', 'product.category'])
    //         ->where('quantity', '<=', 0)
    //         ->orderBy('product_id')
    //         ->paginate(20);

    //     $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();
    //     $etablissement = $etablissements->firstWhere('id', $selectedEtablissementId);

    //     return view('client.stocks.alerts', compact(
    //         'etablissement',
    //         'etablissements',
    //         'selectedEtablissementId',
    //         'lowStock',
    //         'outOfStock'
    //     ));
    // }
/**
 * Alertes de stock minimum
 */
public function alerts(Request $request)
{
    $etablissementIds = $this->getUserEtablissements();
    
    if ($etablissementIds->isEmpty()) {
        return redirect()->route('client.profile.edit')
            ->with('error', 'Vous n\'avez aucun établissement actif.');
    }

    $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();

    // Stock bas
    $lowStock = Stock::where('etablissement_id', $selectedEtablissementId)
        ->with(['product', 'product.category'])
        ->whereRaw('quantity <= minimum_stock')
        ->where('quantity', '>', 0)
        ->orderByRaw('(quantity / minimum_stock) ASC')
        ->paginate(20, ['*'], 'low_page');

    // Rupture de stock
    $outOfStock = Stock::where('etablissement_id', $selectedEtablissementId)
        ->with(['product', 'product.category'])
        ->where('quantity', '<=', 0)
        ->orderBy('product_id')
        ->paginate(20, ['*'], 'out_page');

    // Statistiques
    $totalProducts = Stock::where('etablissement_id', $selectedEtablissementId)->count();
    $normalStock = Stock::where('etablissement_id', $selectedEtablissementId)
        ->whereRaw('quantity > minimum_stock')
        ->where('quantity', '>', 0)
        ->count();

    $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();
    $etablissement = $etablissements->firstWhere('id', $selectedEtablissementId);

    return view('client.stocks.alerts', compact(
        'etablissement',
        'etablissements',
        'selectedEtablissementId',
        'lowStock',
        'outOfStock',
        'totalProducts',
        'normalStock'
    ));
}

/**
 * Valorisation du stock
 */
public function valuation(Request $request)
{
    $etablissementIds = $this->getUserEtablissements();
    
    if ($etablissementIds->isEmpty()) {
        return redirect()->route('client.profile.edit')
            ->with('error', 'Vous n\'avez aucun établissement actif.');
    }

    $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();

    // ===== RÉSUMÉ GLOBAL =====
    $summary = Stock::where('etablissement_id', $selectedEtablissementId)
        ->select(
            DB::raw('COUNT(*) as total_items'),
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(quantity * purchase_price) as total_purchase_value'),
            DB::raw('SUM(quantity * selling_price) as total_selling_value'),
            DB::raw('SUM(quantity * (selling_price - purchase_price)) as total_profit')
        )
        ->first();

    // ===== PAR CATÉGORIE =====
    $byCategory = Stock::where('stocks.etablissement_id', $selectedEtablissementId)
        ->join('products', 'stocks.product_id', '=', 'products.id')
        ->join('categories', 'products.category_id', '=', 'categories.id')
        ->select(
            'categories.id',
            'categories.nom',
            DB::raw('SUM(stocks.quantity) as quantity'),
            DB::raw('SUM(stocks.quantity * stocks.purchase_price) as purchase_value'),
            DB::raw('SUM(stocks.quantity * stocks.selling_price) as selling_value'),
            DB::raw('SUM(stocks.quantity * (stocks.selling_price - stocks.purchase_price)) as profit')
        )
        ->groupBy('categories.id', 'categories.nom')
        ->orderBy('purchase_value', 'desc')
        ->get();

    // ===== TOP PRODUITS =====
    $topProducts = Stock::where('etablissement_id', $selectedEtablissementId)
        ->with('product')
        ->orderBy(DB::raw('quantity * purchase_price'), 'desc')
        ->limit(10)
        ->get(['id', 'product_id', 'quantity', 'purchase_price', 'selling_price']);

    $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();

    return view('client.stocks.valuation', compact(
        'summary',
        'byCategory',
        'topProducts',
        'etablissements',
        'selectedEtablissementId'
    ));
}
    /**
     * Produits en rupture
     */
    //     public function outOfStock(Request $request)
    // {
    //     $etablissementIds = $this->getUserEtablissements();
        
    //     if ($etablissementIds->isEmpty()) {
    //         return redirect()->route('client.profile.edit')
    //             ->with('error', 'Vous n\'avez aucun établissement actif.');
    //     }

    //     $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();

    //     $stocks = Stock::where('etablissement_id', $selectedEtablissementId)
    //         ->with(['product', 'product.category', 'product.unit'])
    //         ->where('quantity', '<=', 0)
    //         ->orderBy('product_id')
    //         ->paginate(20);

    //     $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();
    //     $etablissement = $etablissements->firstWhere('id', $selectedEtablissementId);

    //     return view('client.stocks.out-of-stock', compact(
    //         'etablissement',
    //         'etablissements',
    //         'selectedEtablissementId',
    //         'stocks'
    //     ));
    // }

    /**
     * Valorisation du stock (corrigé)
     */
    // public function valuation(Request $request)
    // {
    //     $etablissementIds = $this->getUserEtablissements();
        
    //     if ($etablissementIds->isEmpty()) {
    //         return redirect()->route('client.profile.edit')
    //             ->with('error', 'Vous n\'avez aucun établissement actif.');
    //     }

    //     $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();

    //     // ===== RÉSUMÉ GLOBAL =====
    //     $summary = Stock::where('etablissement_id', $selectedEtablissementId)
    //         ->select(
    //             DB::raw('COUNT(*) as total_items'),
    //             DB::raw('SUM(quantity) as total_quantity'),
    //             DB::raw('SUM(quantity * purchase_price) as total_purchase_value'),
    //             DB::raw('SUM(quantity * selling_price) as total_selling_value'),
    //             DB::raw('SUM(quantity * (selling_price - purchase_price)) as total_profit')
    //         )
    //         ->first();

    //     // ===== PAR CATÉGORIE (CORRIGÉ) =====
    //     $byCategory = Stock::where('stocks.etablissement_id', $selectedEtablissementId)
    //         ->join('products', 'stocks.product_id', '=', 'products.id')
    //         ->join('categories', 'products.category_id', '=', 'categories.id')
    //         ->select(
    //             'categories.id',
    //             'categories.nom',
    //             DB::raw('SUM(stocks.quantity) as quantity'),
    //             DB::raw('SUM(stocks.quantity * stocks.purchase_price) as purchase_value'),
    //             DB::raw('SUM(stocks.quantity * stocks.selling_price) as selling_value'),
    //             DB::raw('SUM(stocks.quantity * (stocks.selling_price - stocks.purchase_price)) as profit')
    //         )
    //         ->groupBy('categories.id', 'categories.nom')
    //         ->orderBy('purchase_value', 'desc')
    //         ->get();

    //     // ===== TOP PRODUITS PAR VALEUR =====
    //     $topProducts = Stock::where('etablissement_id', $selectedEtablissementId)
    //         ->with('product')
    //         ->orderBy(DB::raw('quantity * purchase_price'), 'desc')
    //         ->limit(10)
    //         ->get(['id', 'product_id', 'quantity', 'purchase_price', 'selling_price']);

    //     $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();
    //     $etablissement = $etablissements->firstWhere('id', $selectedEtablissementId);

    //     return view('client.stocks.valuation', compact(
    //         'etablissement',
    //         'etablissements',
    //         'selectedEtablissementId',
    //         'summary',
    //         'byCategory',
    //         'topProducts'
    //     ));
    // }

    /**
     * Inventaire physique
     */
    public function inventory(Request $request)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();

        if ($request->isMethod('post')) {
            $request->validate([
                'inventory_date' => 'required|date',
                'items' => 'required|array',
                'items.*.stock_id' => 'required|exists:stocks,id',
                'items.*.physical_quantity' => 'required|integer|min:0',
            ]);

            DB::beginTransaction();
            try {
                $inventory = Inventory::create([
                    'etablissement_id' => $selectedEtablissementId,
                    'inventory_date' => $request->inventory_date,
                    'status' => 'pending',
                    'user_id' => Auth::id(),
                    'notes' => $request->notes,
                ]);

                foreach ($request->items as $item) {
                    $stock = Stock::where('etablissement_id', $selectedEtablissementId)
                        ->where('id', $item['stock_id'])
                        ->firstOrFail();

                    $difference = $item['physical_quantity'] - $stock->quantity;

                    \App\Models\InventoryItem::create([
                        'inventory_id' => $inventory->id,
                        'stock_id' => $stock->id,
                        'system_quantity' => $stock->quantity,
                        'physical_quantity' => $item['physical_quantity'],
                        'difference' => $difference,
                    ]);

                    if ($difference != 0) {
                        $oldQuantity = $stock->quantity;
                        $stock->quantity = $item['physical_quantity'];
                        $stock->save();

                        Movement::create([
                            'etablissement_id' => $selectedEtablissementId,
                            'product_id' => $stock->product_id,
                            'type' => $difference > 0 ? 'inventory_in' : 'inventory_out',
                            'quantity' => abs($difference),
                            'before' => $oldQuantity,
                            'after' => $stock->quantity,
                            'purchase_price' => $stock->purchase_price,
                            'selling_price' => $stock->selling_price,
                            'note' => 'Inventaire physique #' . $inventory->id,
                            'user_id' => Auth::id(),
                        ]);
                    }
                }

                $inventory->status = 'completed';
                $inventory->save();

                DB::commit();

                return redirect()->route('client.stocks.index', ['etablissement_id' => $selectedEtablissementId])
                    ->with('success', 'Inventaire physique réalisé avec succès !');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Erreur: ' . $e->getMessage());
            }
        }

        $stocks = Stock::where('etablissement_id', $selectedEtablissementId)
            ->with('product')
            ->orderBy('product_id')
            ->get();

        $inventories = Inventory::where('etablissement_id', $selectedEtablissementId)
            ->with(['items', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();
        $etablissement = $etablissements->firstWhere('id', $selectedEtablissementId);

        return view('client.stocks.inventory', compact(
            'etablissement',
            'etablissements',
            'selectedEtablissementId',
            'stocks',
            'inventories'
        ));
    }
    /**
 * Tableau de bord client
 */
    public function dashboard(Request $request)
{
    // Récupérer tous les établissements de l'utilisateur
    $etablissements = Etablissement::whereHas('users', function ($q) {
        $q->where('users.id', auth()->id());
    })
    ->where('statut', 'actif')
    ->get();

    if ($etablissements->isEmpty()) {
        return redirect()->route('client.profile.edit')
            ->with('error', 'Vous n\'avez aucun établissement actif.');
    }

    $etablissementIds = $etablissements->pluck('id');
    $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();

    if (!$etablissementIds->contains($selectedEtablissementId)) {
        $selectedEtablissementId = $etablissementIds->first();
    }

    $etablissement = $etablissements->firstWhere('id', $selectedEtablissementId);

    // ===== STATISTIQUES =====
    $stats = [
        'total_products' => Stock::where('etablissement_id', $selectedEtablissementId)->sum('quantity'),
        'total_value' => Stock::where('etablissement_id', $selectedEtablissementId)
            ->sum(DB::raw('quantity * purchase_price')),
        'total_selling_value' => Stock::where('etablissement_id', $selectedEtablissementId)
            ->sum(DB::raw('quantity * selling_price')),
        'potential_profit' => Stock::where('etablissement_id', $selectedEtablissementId)
            ->sum(DB::raw('quantity * (selling_price - purchase_price)')),
        'product_count' => Stock::where('etablissement_id', $selectedEtablissementId)->count(),
        'low_stock_count' => Stock::where('etablissement_id', $selectedEtablissementId)
            ->whereRaw('quantity <= minimum_stock')
            ->where('quantity', '>', 0)
            ->count(),
        'out_of_stock_count' => Stock::where('etablissement_id', $selectedEtablissementId)
            ->where('quantity', '<=', 0)
            ->count(),
    ];

    // ===== STOCK PAR PRODUIT =====
    $stockByEtablissement = Stock::with(['product', 'product.category', 'product.unit'])
        ->where('etablissement_id', $selectedEtablissementId)
        ->orderBy('quantity', 'desc')
        ->get();

    // ===== DERNIERS MOUVEMENTS =====
    $recentMovements = Movement::with(['product', 'user'])
        ->where('etablissement_id', $selectedEtablissementId)
        ->orderBy('created_at', 'desc')
        ->limit(20)
        ->get();

    // ===== ALERTES =====
    $alerts = collect();

    // Stock bas
    $lowStock = Stock::with(['product', 'product.category'])
        ->where('etablissement_id', $selectedEtablissementId)
        ->whereRaw('quantity <= minimum_stock')
        ->where('quantity', '>', 0)
        ->get();

    foreach ($lowStock as $item) {
        $alerts->push([
            'product' => $item->product->name,
            'category' => $item->product->category->nom ?? 'N/A',
            'quantity' => $item->quantity,
            'minimum' => $item->minimum_stock,
            'percentage' => round(($item->quantity / $item->minimum_stock) * 100),
            'type' => 'low_stock',
        ]);
    }

    // Rupture de stock
    $outOfStock = Stock::with(['product', 'product.category'])
        ->where('etablissement_id', $selectedEtablissementId)
        ->where('quantity', '<=', 0)
        ->get();

    foreach ($outOfStock as $item) {
        $alerts->push([
            'product' => $item->product->name,
            'category' => $item->product->category->nom ?? 'N/A',
            'quantity' => 0,
            'minimum' => $item->minimum_stock,
            'percentage' => 0,
            'type' => 'out_of_stock',
        ]);
    }

    $alerts = $alerts->sortBy('percentage')->take(10);

    // ===== VALORISATION =====
    $stockValuation = Stock::where('etablissement_id', $selectedEtablissementId)
        ->select(
            DB::raw('SUM(quantity) as total_quantity'),
            DB::raw('SUM(quantity * purchase_price) as total_purchase_value'),
            DB::raw('SUM(quantity * selling_price) as total_selling_value'),
            DB::raw('SUM(quantity * (selling_price - purchase_price)) as total_profit')
        )
        ->first();

    return view('client.dashboard', compact(
        'etablissements',
        'etablissement',
        'selectedEtablissementId',
        'stats',
        'stockByEtablissement',
        'recentMovements',
        'alerts',
        'stockValuation'
    ));
}
}