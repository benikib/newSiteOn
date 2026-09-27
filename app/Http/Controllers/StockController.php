<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use App\Models\Etablissement;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Afficher la liste des stocks
     */
    public function index(Request $request)
    {
        // ===== QUERY DE BASE =====
        $query = Stock::query()
            ->with(['etablissement', 'product', 'product.category', 'product.unit']);

        // ===== FILTRES =====
        // Recherche par produit ou établissement
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('product', function($q2) use ($search) {
                    $q2->where('name', 'LIKE', '%' . $search . '%')
                       ->orWhere('code', 'LIKE', '%' . $search . '%');
                })->orWhereHas('etablissement', function($q2) use ($search) {
                    $q2->where('name', 'LIKE', '%' . $search . '%');
                });
            });
        }

        // Filtre par établissement
        if ($request->filled('etablissement_id')) {
            $query->where('etablissement_id', $request->etablissement_id);
        }

        // Filtre par produit
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filtre par stock minimum
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low') {
                $query->whereRaw('quantity <= minimum_stock');
            } elseif ($request->stock_status === 'out') {
                $query->where('quantity', '<=', 0);
            } elseif ($request->stock_status === 'in') {
                $query->whereRaw('quantity > minimum_stock');
            }
        }

        // ===== TRI =====
        switch ($request->sort) {
            case 'product_asc':
                $query->orderBy(Product::select('name')->whereColumn('products.id', 'stocks.product_id'), 'asc');
                break;
            case 'product_desc':
                $query->orderBy(Product::select('name')->whereColumn('products.id', 'stocks.product_id'), 'desc');
                break;
            case 'quantity_asc':
                $query->orderBy('quantity', 'asc');
                break;
            case 'quantity_desc':
                $query->orderBy('quantity', 'desc');
                break;
            case 'selling_price_asc':
                $query->orderBy('selling_price', 'asc');
                break;
            case 'selling_price_desc':
                $query->orderBy('selling_price', 'desc');
                break;
            default:
                $query->orderBy('updated_at', 'desc');
                break;
        }

        // ===== PAGINATION =====
        $perPage = $request->per_page ?? 15;
        $stocks = $query->paginate($perPage);

        // ===== STATISTIQUES =====
        $stats = [
            'total_products' => Stock::sum('quantity'),
            'total_value' => Stock::sum(DB::raw('quantity * purchase_price')),
            'total_selling_value' => Stock::sum(DB::raw('quantity * selling_price')),
            'low_stock' => Stock::whereRaw('quantity <= minimum_stock')->count(),
            'out_of_stock' => Stock::where('quantity', '<=', 0)->count(),
            'total_etablissements' => Etablissement::count(),
        ];

        // ===== DONNÉES POUR LES FILTRES =====
        $etablissements = Etablissement::orderBy('nom')->get();
        $products = Product::where('status', true)->orderBy('name')->get();

        // ===== SI REQUÊTE AJAX =====
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $stocks,
                'stats' => $stats,
                'pagination' => [
                    'current_page' => $stocks->currentPage(),
                    'last_page' => $stocks->lastPage(),
                    'per_page' => $stocks->perPage(),
                    'total' => $stocks->total(),
                ]
            ]);
        }

        // ===== VUE =====
        return view('admins.stock.stockadmin.index', compact('stocks', 'stats', 'etablissements', 'products'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $etablissements = Etablissement::orderBy('nom')->get();
        $products = Product::where('status', true)->orderBy('name')->get();

        return view('admins.stock.stockadmin.create', compact('etablissements', 'products'));
    }

    /**
     * Enregistrer un nouveau stock
     */
    public function store(Request $request)
    {
        // ===== VALIDATION =====
        $validator = Validator::make($request->all(), [
            'etablissement_id' => 'required|exists:etablissements,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        // ===== VÉRIFICATION DOUBLON =====
        $existingStock = Stock::where('etablissement_id', $request->etablissement_id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingStock) {
            $error = 'Ce produit existe déjà dans cet établissement. Utilisez la modification.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $error
                ], 422);
            }
            return back()->with('error', $error)->withInput();
        }

        // ===== CRÉATION =====
        $stock = Stock::create([
            'etablissement_id' => $request->etablissement_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
            'minimum_stock' => $request->minimum_stock ?? 0,
        ]);

        // ===== RÉPONSE =====
        $message = 'Stock ajouté avec succès !';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $stock->load(['etablissement', 'product'])
            ]);
        }

        return redirect()->route('admin.stocks.index')
            ->with('success', $message);
    }

    /**
     * Afficher un stock spécifique
     */
    public function show(Stock $stock)
    {
        $stock->load(['etablissement', 'product', 'product.category', 'product.unit']);
        
        // Historique des mouvements (si vous avez une table movements)
        // $movements = $stock->movements()->latest()->paginate(10);

        return view('admins.stock.stockadmin.show', compact('stock'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Stock $stock)
    {
        $etablissements = Etablissement::orderBy('nom')->get();
        $products = Product::where('status', true)->orderBy('name')->get();

        return view('admins.stock.stockadmin.edit', compact('stock', 'etablissements', 'products'));
    }

    /**
     * Mettre à jour un stock
     */
    public function update(Request $request, Stock $stock)
    {
        // ===== VALIDATION =====
        $validator = Validator::make($request->all(), [
            'etablissement_id' => 'required|exists:etablissements,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        // ===== VÉRIFICATION DOUBLON =====
        $existingStock = Stock::where('etablissement_id', $request->etablissement_id)
            ->where('product_id', $request->product_id)
            ->where('id', '!=', $stock->id)
            ->first();

        if ($existingStock) {
            $error = 'Ce produit existe déjà dans cet établissement.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $error
                ], 422);
            }
            return back()->with('error', $error)->withInput();
        }

        // ===== MISE À JOUR =====
        $stock->update([
            'etablissement_id' => $request->etablissement_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
            'minimum_stock' => $request->minimum_stock ?? 0,
        ]);

        // ===== RÉPONSE =====
        $message = 'Stock mis à jour avec succès !';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $stock->load(['etablissement', 'product'])
            ]);
        }

        return redirect()->route('admin.stocks.index')
            ->with('success', $message);
    }

    /**
     * Supprimer un stock
     */
    public function destroy(Request $request, Stock $stock)
    {
        $stockName = $stock->product->name . ' - ' . $stock->etablissement->nom;
        $stock->delete();

        $message = 'Stock "' . $stockName . '" supprimé avec succès !';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_id' => $stock->id
            ]);
        }

        return redirect()->route('admin.stocks.index')
            ->with('success', $message);
    }

    /**
     * Ajouter de la quantité au stock
     */
    public function addQuantity(Request $request, Stock $stock)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $oldQuantity = $stock->quantity;
        $stock->quantity += $request->quantity;

        // Mise à jour du prix d'achat (moyenne pondérée)
        if ($request->filled('purchase_price')) {
            $totalCost = ($oldQuantity * $stock->purchase_price) + ($request->quantity * $request->purchase_price);
            $stock->purchase_price = $totalCost / $stock->quantity;
        }

        $stock->save();

        // Enregistrer l'historique (si vous avez une table movement)
        // Mouvement::create([
        //     'stock_id' => $stock->id,
        //     'type' => 'in',
        //     'quantity' => $request->quantity,
        //     'before' => $oldQuantity,
        //     'after' => $stock->quantity,
        //     'note' => $request->note,
        //     'user_id' => auth()->id(),
        // ]);

        return response()->json([
            'success' => true,
            'message' => $request->quantity . ' unité(s) ajoutée(s) au stock',
            'data' => $stock
        ]);
    }

    /**
     * Retirer de la quantité du stock
     */
    public function removeQuantity(Request $request, Stock $stock)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1|max:' . $stock->quantity,
            'note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $oldQuantity = $stock->quantity;
        $stock->quantity -= $request->quantity;
        $stock->save();

        // Enregistrer l'historique
        // Mouvement::create([
        //     'stock_id' => $stock->id,
        //     'type' => 'out',
        //     'quantity' => $request->quantity,
        //     'before' => $oldQuantity,
        //     'after' => $stock->quantity,
        //     'note' => $request->note,
        //     'user_id' => auth()->id(),
        // ]);

        return response()->json([
            'success' => true,
            'message' => $request->quantity . ' unité(s) retirée(s) du stock',
            'data' => $stock
        ]);
    }

    /**
     * Exporter les stocks en CSV
     */
    public function export(Request $request)
    {
        $stocks = Stock::with(['etablissement', 'product', 'product.category', 'product.unit'])->get();

        $filename = 'stocks_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($stocks) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'ID', 'Établissement', 'Produit', 'Code', 'Catégorie', 'Unité',
                'Quantité', "Prix d'achat", 'Prix de vente', 'Stock minimum', 
                'Valeur du stock', 'status', 'Dernière mise à jour'
            ]);

            // Données
            foreach ($stocks as $stock) {
                $status = 'OK';
                if ($stock->quantity <= 0) {
                    $status = 'Rupture';
                } elseif ($stock->quantity <= $stock->minimum_stock) {
                    $status = 'Stock bas';
                }

                fputcsv($file, [
                    $stock->id,
                    $stock->etablissement->nom ?? 'N/A',
                    $stock->product->name ?? 'N/A',
                    $stock->product->code ?? 'N/A',
                    $stock->product->category->nom ?? 'N/A',
                    $stock->product->unit->name ?? 'N/A',
                    $stock->quantity,
                    number_format($stock->purchase_price, 2, ',', ' '),
                    number_format($stock->selling_price, 2, ',', ' '),
                    $stock->minimum_stock,
                    number_format($stock->quantity * $stock->purchase_price, 2, ',', ' '),
                    $status,
                    $stock->updated_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Tableau de bord des stocks
     */
    public function dashboard(Request $request)
    {
        // ===== STATISTIQUES GÉNÉRALES =====
        $stats = [
            'total_products' => Stock::sum('quantity'),
            'total_value' => Stock::sum(DB::raw('quantity * purchase_price')),
            'total_selling_value' => Stock::sum(DB::raw('quantity * selling_price')),
            'potential_profit' => Stock::sum(DB::raw('quantity * (selling_price - purchase_price)')),
            'low_stock_count' => Stock::whereRaw('quantity <= minimum_stock')->count(),
            'out_of_stock_count' => Stock::where('quantity', '<=', 0)->count(),
            'etablissement_count' => Etablissement::count(),
            'product_count' => Product::count(),
        ];

        // ===== TOP PRODUITS =====
        $topProducts = Stock::with(['product', 'etablissement'])
            ->orderBy('quantity', 'desc')
            ->limit(10)
            ->get();

        // ===== PRODUITS EN RUPTURE =====
        $outOfStock = Stock::with(['product', 'etablissement'])
            ->where('quantity', '<=', 0)
            ->limit(10)
            ->get();

        // ===== PRODUITS EN STOCK BAS =====
        $lowStock = Stock::with(['product', 'etablissement'])
            ->whereRaw('quantity <= minimum_stock')
            ->where('quantity', '>', 0)
            ->limit(10)
            ->get();

       // ===== VALEUR PAR ÉTABLISSEMENT =====
$valueByEtablissement = Etablissement::with(['stocks'])
    ->get()
    ->map(function($etab) {
        // Vérifier si l'établissement a des stocks
        $stocks = $etab->stocks ?? collect();
        
        return [
            'id' => $etab->id,
            'name' => $etab->nom ?? $etab->name ?? 'Sans nom',
            'total_value' => $stocks->sum(function($stock) {
                return ($stock->quantity ?? 0) * ($stock->purchase_price ?? 0);
            }),
            'total_products' => $stocks->sum('quantity'),
        ];
    })
    ->filter(function($item) {
        // Garder uniquement les établissements avec une valeur > 0 ou des produits
        return $item['total_value'] > 0 || $item['total_products'] > 0;
    })
    ->sortByDesc('total_value')
    ->take(10)
    ->values();

        // ===== SI REQUÊTE AJAX =====
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'top_products' => $topProducts,
                    'out_of_stock' => $outOfStock,
                    'low_stock' => $lowStock,
                    'value_by_etablissement' => $valueByEtablissement,
                ]
            ]);
        }

        return view('admins.stock.dashboard', compact(
            'stats', 'topProducts', 'outOfStock', 'lowStock', 'valueByEtablissement'
        ));
    }

    /**
     * Recherche rapide de stock
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2'
        ]);

        $stocks = Stock::with(['product', 'etablissement'])
            ->whereHas('product', function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%')
                  ->orWhere('code', 'LIKE', '%' . $request->q . '%');
            })
            ->orWhereHas('etablissement', function($q) use ($request) {
                $q->where('nom', 'LIKE', '%' . $request->q . '%');
            })
            ->limit(10)
            ->get(['id', 'product_id', 'etablissement_id', 'quantity', 'selling_price', 'minimum_stock']);

        return response()->json([
            'success' => true,
            'data' => $stocks
        ]);
    }
}