<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Etablissement;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Movement;
use App\Models\Inventory;
use App\Models\TauxDeChange;
use App\Services\PurchasePrice;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

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

    private function resolveEtablissementId(Request $request, $etablissementIds)
    {
        $requestedId = $request->input('etablissement_id');

        return $etablissementIds->contains($requestedId)
            ? $requestedId
            : $etablissementIds->first();
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
        $selectedEtablissementId = $this->resolveEtablissementId($request, $etablissementIds);

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

        $selectedEtablissementId = $this->resolveEtablissementId($request, $etablissementIds);
        
        if (!$etablissementIds->contains($selectedEtablissementId)) {
            $selectedEtablissementId = $etablissementIds->first();
        }

        $stocks = $this->filteredStocksQuery($request, $selectedEtablissementId)
            ->paginate(20);

        $categories = \App\Models\Category::where('status', true)->get();
        $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();

        return view('client.stocks.index', compact(
            'stocks',
            'categories',
            'etablissements',
            'selectedEtablissementId'
        ));
    }

    public function printStocks(Request $request)
    {
        $data = $this->stockListExportData($request);
        if ($data === null) {
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        return view('client.stocks.export', $data + ['showPrintButton' => true]);
    }

    public function pdfStocks(Request $request)
    {
        $data = $this->stockListExportData($request);
        if ($data === null) {
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        return Pdf::loadView('client.stocks.export', $data + ['showPrintButton' => false])
            ->setPaper('a4', 'landscape')
            ->download('stocks-' . $data['etablissement']->id . '.pdf');
    }

    private function stockListExportData(Request $request): ?array
    {
        $etablissementIds = $this->getUserEtablissements();
        if ($etablissementIds->isEmpty()) {
            return null;
        }

        $selectedEtablissementId = $this->resolveEtablissementId($request, $etablissementIds);
        if (!$etablissementIds->contains($selectedEtablissementId)) {
            $selectedEtablissementId = $etablissementIds->first();
        }

        return [
            'etablissement' => Etablissement::findOrFail($selectedEtablissementId),
            'stocks' => $this->filteredStocksQuery($request, $selectedEtablissementId)->get(),
            'printedAt' => now(),
        ];
    }

    private function filteredStocksQuery(Request $request, $etablissementId)
    {
        $query = Stock::with(['product', 'product.category', 'product.unit'])
            ->where('etablissement_id', $etablissementId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('code', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->whereHas('product', function ($q) use ($request) {
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

        return $query->orderBy('quantity', 'asc');
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

        $selectedEtablissementId = $this->resolveEtablissementId($request, $etablissementIds);
        
        if (!$etablissementIds->contains($selectedEtablissementId)) {
            $selectedEtablissementId = $etablissementIds->first();
        }

        if ($request->isMethod('post')) {
            if (!$request->has('items')) {
                $request->merge([
                    'items' => [[
                        'product_id' => $request->input('product_id'),
                        'quantity' => $request->input('quantity'),
                        'purchase_price' => $request->input('purchase_price'),
                        'purchase_currency' => $request->input('purchase_currency'),
                        'selling_price' => $request->input('selling_price'),
                        'selling_currency' => $request->input('selling_currency'),
                    ]],
                ]);
            }

            $validated = $request->validate([
                'etablissement_id' => 'required|in:' . $etablissementIds->implode(','),
                'items' => 'required|array|min:1',
                'items.*.product_id' => [
                    'required',
                    'distinct',
                    Rule::exists('products', 'id')->where('etablissement_id', $request->input('etablissement_id')),
                ],
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.purchase_price' => 'required|numeric|min:0',
                'items.*.purchase_currency' => 'required|in:CDF,USD',
                'items.*.selling_price' => 'nullable|numeric|min:0',
                'items.*.selling_currency' => 'nullable|in:CDF,USD',
                'note' => 'nullable|string|max:500',
            ]);

            try {
                DB::transaction(function () use ($validated, $request) {
                    foreach ($validated['items'] as $item) {
                        $stock = Stock::where('etablissement_id', $validated['etablissement_id'])
                            ->where('product_id', $item['product_id'])
                            ->lockForUpdate()
                            ->first();

                        [$purchasePriceCdf, $exchangeRate] = PurchasePrice::normalize(
                            (float) $item['purchase_price'],
                            $item['purchase_currency']
                        );
                        if (isset($item['selling_price']) && $item['selling_price'] !== '') {
                            $sellingPriceOriginal = (float) $item['selling_price'];
                            $sellingCurrency = $item['selling_currency'] ?? 'CDF';
                            [$sellingPriceCdf, $sellingExchangeRate] = PurchasePrice::normalize(
                                $sellingPriceOriginal,
                                $sellingCurrency,
                                'selling_currency'
                            );
                        } else {
                            $sellingPriceCdf = (float) ($stock->selling_price ?? 0);
                            $sellingPriceOriginal = (float) ($stock->selling_price_original ?? $sellingPriceCdf);
                            $sellingCurrency = $stock->selling_currency ?? 'CDF';
                            $sellingExchangeRate = $stock->selling_exchange_rate ?? null;
                        }

                        if ($stock) {
                            $oldQuantity = $stock->quantity;
                            $oldPurchasePrice = $stock->purchase_price;
                            $newQuantity = $oldQuantity + (int) $item['quantity'];
                            $stock->quantity = $newQuantity;
                            $stock->purchase_price = (($oldQuantity * $oldPurchasePrice)
                                + ((int) $item['quantity'] * $purchasePriceCdf)) / $newQuantity;
                            $stock->purchase_price_original = $item['purchase_price'];
                            $stock->purchase_currency = $item['purchase_currency'];
                            $stock->purchase_exchange_rate = $exchangeRate;
                            $stock->selling_price = $sellingPriceCdf;
                            $stock->selling_price_original = $sellingPriceOriginal;
                            $stock->selling_currency = $sellingCurrency;
                            $stock->selling_exchange_rate = $sellingExchangeRate;
                            $stock->save();
                        } else {
                            $oldQuantity = 0;
                            $stock = Stock::create([
                                'etablissement_id' => $validated['etablissement_id'],
                                'product_id' => $item['product_id'],
                                'quantity' => $item['quantity'],
                                'purchase_price' => $purchasePriceCdf,
                                'purchase_price_original' => $item['purchase_price'],
                                'purchase_currency' => $item['purchase_currency'],
                                'purchase_exchange_rate' => $exchangeRate,
                                'selling_price' => $sellingPriceCdf,
                                'selling_price_original' => $sellingPriceOriginal,
                                'selling_currency' => $sellingCurrency,
                                'selling_exchange_rate' => $sellingExchangeRate,
                                'minimum_stock' => 0,
                            ]);
                        }

                        Movement::create([
                            'etablissement_id' => $validated['etablissement_id'],
                            'product_id' => $item['product_id'],
                            'type' => 'in',
                            'quantity' => $item['quantity'],
                            'before' => $oldQuantity,
                            'after' => $stock->quantity,
                            'purchase_price' => $purchasePriceCdf,
                            'purchase_price_original' => $item['purchase_price'],
                            'purchase_currency' => $item['purchase_currency'],
                            'purchase_exchange_rate' => $exchangeRate,
                            'selling_price' => $sellingPriceCdf,
                            'selling_price_original' => $sellingPriceOriginal,
                            'selling_currency' => $sellingCurrency,
                            'selling_exchange_rate' => $sellingExchangeRate,
                            'note' => $request->input('note') ?? 'Approvisionnement',
                            'user_id' => Auth::id(),
                        ]);
                    }
                });

                $message = count($validated['items']) > 1
                    ? 'Les stocks ont été enregistrés avec succès !'
                    : 'Stock ajouté avec succès !';

                if ($request->expectsJson()) {
                    return response()->json(['success' => true, 'message' => $message]);
                }

                return redirect()->route('client.stocks.stock-in', [
                    'etablissement_id' => $validated['etablissement_id'],
                ])->with('success', $message);
            } catch (ValidationException $e) {
                throw $e;
            } catch (\Exception $e) {
                report($e);

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Impossible d’enregistrer ces entrées de stock. Vérifiez le taux de change et réessayez.',
                    ], 500);
                }

                return back()->with('error', 'Impossible d’enregistrer cette entrée de stock. Réessayez.');
            }
        }

        $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();
        $etablissement = $etablissements->firstWhere('id', $selectedEtablissementId);

        $products = Product::where('etablissement_id', $selectedEtablissementId)
            ->where('status', true)
            ->with('stock')
            ->orderBy('name')
            ->get();
        $usdCdfRate = TauxDeChange::orderByDesc('date')->value('usd_cdf');

        return view('client.stocks.stock-in', compact(
            'etablissement',
            'etablissements',
            'selectedEtablissementId',
            'products',
            'usdCdfRate'
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

        $selectedEtablissementId = $this->resolveEtablissementId($request, $etablissementIds);

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
                report($e);
                return back()->with('error', 'Impossible d’effectuer cette sortie de stock. Réessayez.');
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

    $selectedEtablissementId = $this->resolveEtablissementId($request, $etablissementIds);

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

        $selectedEtablissementId = $this->resolveEtablissementId($request, $etablissementIds);

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
                report($e);
                return back()->with('error', 'Impossible d’ajuster le stock. Réessayez.');
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

    $selectedEtablissementId = $this->resolveEtablissementId($request, $etablissementIds);
    if (!$etablissementIds->contains($selectedEtablissementId)) {
        $selectedEtablissementId = $etablissementIds->first();
    }

    $movements = $this->filteredMovementsQuery($request, $selectedEtablissementId)
        ->orderByDesc('created_at')
        ->paginate(30)
        ->withQueryString();

    $totalIn = Movement::where('etablissement_id', $selectedEtablissementId)->where('type', 'in')->count();
    $totalOut = Movement::where('etablissement_id', $selectedEtablissementId)->where('type', 'out')->count();
    $totalAdjust = Movement::where('etablissement_id', $selectedEtablissementId)
        ->whereIn('type', ['adjust_positive', 'adjust_negative'])->count();
    $products = Product::whereHas('stock', function ($query) use ($selectedEtablissementId) {
        $query->where('etablissement_id', $selectedEtablissementId);
    })->get();
    $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();

    return view('client.stocks.movements', compact(
        'movements', 'products', 'etablissements', 'selectedEtablissementId', 'totalIn', 'totalOut', 'totalAdjust'
    ));
}

public function printMovements(Request $request)
{
    $data = $this->movementExportData($request);
    if ($data === null) {
        return redirect()->route('client.profile.edit')->with('error', 'Vous n\'avez aucun établissement actif.');
    }

    return view('client.stocks.movements-export', $data + ['showPrintButton' => true]);
}

public function pdfMovements(Request $request)
{
    $data = $this->movementExportData($request);
    if ($data === null) {
        return redirect()->route('client.profile.edit')->with('error', 'Vous n\'avez aucun établissement actif.');
    }

    return Pdf::loadView('client.stocks.movements-export', $data + ['showPrintButton' => false])
        ->setPaper('a4', 'landscape')
        ->download('mouvements-stock-' . $data['etablissement']->id . '.pdf');
}

private function movementExportData(Request $request): ?array
{
    $etablissementIds = $this->getUserEtablissements();
    if ($etablissementIds->isEmpty()) {
        return null;
    }

    $selectedEtablissementId = $this->resolveEtablissementId($request, $etablissementIds);
    if (!$etablissementIds->contains($selectedEtablissementId)) {
        $selectedEtablissementId = $etablissementIds->first();
    }

    $movements = $this->filteredMovementsQuery($request, $selectedEtablissementId)
        ->orderByDesc('created_at')
        ->get();

    $firstDate = $movements->min('created_at');
    $lastDate = $movements->max('created_at');
    if ($request->filled('date_from') || $request->filled('date_to')) {
        $start = $request->date_from
            ? \Carbon\Carbon::parse($request->date_from)->format('d/m/Y')
            : 'début';
        $end = $request->date_to
            ? \Carbon\Carbon::parse($request->date_to)->format('d/m/Y')
            : 'aujourd’hui';
        $period = "Du {$start} au {$end}";
    } elseif ($firstDate && $lastDate) {
        $period = 'Du ' . $firstDate->format('d/m/Y') . ' au ' . $lastDate->format('d/m/Y');
    } else {
        $period = 'Toutes les dates (aucun mouvement)';
    }

    return [
        'etablissement' => Etablissement::find($selectedEtablissementId),
        'movements' => $movements,
        'period' => $period,
        'printedAt' => now(),
    ];
}

private function filteredMovementsQuery(Request $request, $etablissementId)
{
    return Movement::where('etablissement_id', $etablissementId)
        ->with(['product', 'user'])
        ->when($request->filled('type'), fn ($query) => $query->where('type', $request->type))
        ->when($request->filled('product_id'), fn ($query) => $query->where('product_id', $request->product_id))
        ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date_from))
        ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date_to));
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

    $selectedEtablissementId = $this->resolveEtablissementId($request, $etablissementIds);

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

    $selectedEtablissementId = $this->resolveEtablissementId($request, $etablissementIds);

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

        $selectedEtablissementId = $this->resolveEtablissementId($request, $etablissementIds);

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
                report($e);
                return back()->with('error', 'Impossible d’enregistrer cet inventaire. Réessayez.');
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
    $selectedEtablissementId = $this->resolveEtablissementId($request, $etablissementIds);

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