<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Afficher la liste des catégories avec le nombre de produits
     */
    public function index(Request $request)
    {
        // ===== QUERY DE BASE AVEC JOINTURE PRODUCTS =====
        $query = Category::query()
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->select('categories.*')
            ->selectRaw('COUNT(products.id) as products_count')
            ->groupBy('categories.id', 'categories.nom', 'categories.description', 'categories.status', 'categories.created_at', 'categories.updated_at');

        // ===== FILTRES =====
        // Recherche par nom
        if ($request->filled('search')) {
            $query->where('categories.nom', 'LIKE', '%' . $request->search . '%');
        }

        // Filtre par status (boolean)
        if ($request->filled('status')) {
            $query->where('categories.status', $request->status === 'actif' ? 1 : 0);
        }

        // Filtre par nombre de produits
        if ($request->filled('has_products')) {
            if ($request->has_products === 'yes') {
                $query->having('products_count', '>', 0);
            } elseif ($request->has_products === 'no') {
                $query->having('products_count', '=', 0);
            }
        }

        // ===== TRI =====
        switch ($request->sort) {
            case 'nom_asc':
                $query->orderBy('categories.nom', 'asc');
                break;
            case 'nom_desc':
                $query->orderBy('categories.nom', 'desc');
                break;
            case 'products_desc':
                $query->orderBy('products_count', 'desc');
                break;
            case 'products_asc':
                $query->orderBy('products_count', 'asc');
                break;
            case 'created_desc':
                $query->orderBy('categories.created_at', 'desc');
                break;
            case 'created_asc':
                $query->orderBy('categories.created_at', 'asc');
                break;
            default:
                $query->orderBy('categories.nom', 'asc');
                break;
        }

        // ===== PAGINATION =====
        $perPage = $request->per_page ?? 12;
        $categories = $query->paginate($perPage);

        // ===== STATISTIQUES AVEC JOINTURE PRODUCTS =====
        $stats = [
            'total' => Category::count(),
            'actif' => Category::where('status', true)->count(),
            'inactif' => Category::where('status', false)->count(),
            'with_products' => Category::has('products')->count(),
            'without_products' => Category::doesntHave('products')->count(),
            'total_products' => Product::count(),
            'avg_products_per_category' => round(Product::count() / max(Category::count(), 1), 1),
        ];

        // ===== SI REQUÊTE AJAX =====
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $categories,
                'stats' => $stats,
                'pagination' => [
                    'current_page' => $categories->currentPage(),
                    'last_page' => $categories->lastPage(),
                    'per_page' => $categories->perPage(),
                    'total' => $categories->total(),
                ]
            ]);
        }

        // ===== VUE =====
        return view('admins.stock.categories.index', compact('categories', 'stats'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('admins.stock.categories.create');
    }

    /**
     * Enregistrer une nouvelle catégorie
     */
    public function store(Request $request)
    {
        // ===== VALIDATION =====
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:categories,nom',
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|boolean',
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

        // ===== CRÉATION =====
        $category = Category::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'status' => $request->has('status') ? $request->status : true,
        ]);

        // ===== RÉPONSE =====
        $message = 'Catégorie "' . $category->nom . '" créée avec succès !';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $category->loadCount('products')
            ]);
        }

        return redirect()->route('stock.categories.index')
            ->with('success', $message);
    }

    /**
     * Afficher une catégorie spécifique avec ses produits
     */
    public function show(Category $category)
    {
        // Charger les produits de la catégorie avec pagination
        $products = $category->products()
            ->with(['unit', 'supplier'])
            ->paginate(20);

        $stats = [
            'products_count' => $category->products()->count(),
            'total_stock_value' => $category->products()->count() ?? 0,
            // 'total_quantity' => $category->products()->sum('quantity') ?? 0,
            // 'low_stock_products' => $category->products()->where('quantity', '<=', 'alert_quantity')->count(),
            // 'out_of_stock_products' => $category->products()->where('quantity', '<=', 0)->count(),
        ];

        return view('admins.stock.categories.show', compact('category', 'products', 'stats'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Category $category)
    {
        return view('admins.stock.categories.edit', compact('category'));
    }

    /**
     * Mettre à jour une catégorie
     */
    public function update(Request $request, Category $category)
    {
        // ===== VALIDATION =====
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:categories,nom,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|boolean',
        ]);
        

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            dd($validator);
            return back()->withErrors($validator)->withInput();
        }

        // ===== MISE À JOUR =====
        
        $category->update([
            'nom' => $request->nom,
            'description' => $request->description,
            'status' => $request->has('status') ? $request->status : false,
        ]);

        // ===== RÉPONSE =====
        $message = 'Catégorie "' . $category->nom . '" mise à jour avec succès !';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $category->loadCount('products')
            ]);
        }

        return redirect()->route('stock.categories.index')
            ->with('success', $message);
    }

    /**
     * Supprimer une catégorie
     */
    public function destroy(Request $request, Category $category)
    {
        // ===== VÉRIFICATION DES PRODUITS ASSOCIÉS =====
        $productsCount = $category->products()->count();

        if ($productsCount > 0) {
            $error = 'Cette catégorie contient ' . $productsCount . ' produit(s). '
                   . 'Veuillez les déplacer avant de supprimer cette catégorie.';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $error,
                    'products_count' => $productsCount,
                ], 422);
            }
            return back()->with('error', $error);
        }

        // ===== SUPPRESSION =====
        $categoryNom = $category->nom;
        $category->delete();

        // ===== RÉPONSE =====
        $message = 'Catégorie "' . $categoryNom . '" supprimée avec succès !';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_id' => $category->id
            ]);
        }

        return redirect()->route('stock.categories.index')
            ->with('success', $message);
    }

    /**
     * Déplacer les produits d'une catégorie vers une autre
     */
    public function moveProducts(Request $request, Category $category)
    {
        // ===== VALIDATION =====
        $validator = Validator::make($request->all(), [
            'target_category_id' => 'required|exists:categories,id|different:category_id',
            'confirm' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator);
        }

        // ===== DÉPLACEMENT DES PRODUITS =====
        $targetCategory = Category::find($request->target_category_id);
        $movedCount = $category->products()->update([
            'category_id' => $targetCategory->id
        ]);

        // ===== RÉPONSE =====
        $message = $movedCount . ' produit(s) déplacé(s) vers "' . $targetCategory->nom . '" avec succès !';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'moved_count' => $movedCount
            ]);
        }

        return redirect()->route('stock.categories.index')
            ->with('success', $message);
    }

    /**
     * Activer/Désactiver une catégorie
     */
    public function toggleStatus(Request $request, Category $category)
    {
        $newStatus = !$category->status;
        $category->update(['status' => $newStatus]);

        $message = 'Catégorie "' . $category->nom . '" ' . ($newStatus ? 'activée' : 'désactivée') . ' avec succès !';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $category->loadCount('products')
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Exporter les catégories en CSV avec le nombre de produits
     */
    public function export(Request $request)
    {
        $categories = Category::withCount('products')->get();

        $filename = 'categories_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($categories) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'ID', 
                'Nom', 
                'Description', 
                'status', 
                'Nombre de produits',
                'Valeur totale du stock',
                'Créé le',
                'Dernière mise à jour'
            ]);

            // Données
            foreach ($categories as $category) {
                $totalValue = $category->products()->sum('stock_value') ?? 0;
                
                fputcsv($file, [
                    $category->id,
                    $category->nom,
                    $category->description ?? '',
                    $category->status ? 'Actif' : 'Inactif',
                    $category->products_count ?? 0,
                    number_format($totalValue, 2, ',', ' ') . ' Fc',
                    $category->created_at->format('d/m/Y H:i'),
                    $category->updated_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Importer des catégories depuis un fichier CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        
        $headers = fgetcsv($handle);
        $imported = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            try {
                $data = array_combine($headers, $row);
                
                // Création ou mise à jour
                $category = Category::updateOrCreate(
                    ['nom' => $data['Nom']],
                    [
                        'description' => $data['Description'] ?? null,
                        'status' => isset($data['status']) && $data['status'] === 'Inactif' ? false : true,
                    ]
                );

                $imported++;

            } catch (\Exception $e) {
                $errors[] = 'Erreur ligne ' . ($imported + 2) . ': ' . $e->getMessage();
            }
        }

        fclose($handle);

        $message = $imported . ' catégorie(s) importée(s) avec succès !';
        if (!empty($errors)) {
            $message .= ' ' . count($errors) . ' erreur(s) rencontrée(s).';
        }

        return back()->with('success', $message)->with('import_errors', $errors);
    }

    /**
     * Recherche rapide de catégories avec comptage de produits
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2'
        ]);

        $categories = Category::where('nom', 'LIKE', '%' . $request->q . '%')
            ->withCount('products')
            ->limit(10)
            ->get(['id', 'nom', 'status', 'description']);

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Obtenir les statistiques des catégories
     */
    public function stats(Request $request)
    {
        $stats = [
            'total_categories' => Category::count(),
            'active_categories' => Category::where('status', true)->count(),
            'inactive_categories' => Category::where('status', false)->count(),
            'categories_with_products' => Category::has('products')->count(),
            'categories_without_products' => Category::doesntHave('products')->count(),
            'total_products' => Product::count(),
            'avg_products_per_category' => round(Product::count() / max(Category::count(), 1), 1),
            'max_products_in_category' => Category::withCount('products')->orderBy('products_count', 'desc')->first()?->products_count ?? 0,
            'top_categories' => Category::withCount('products')
                ->orderBy('products_count', 'desc')
                ->limit(5)
                ->get(['id', 'nom', 'products_count']),
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        }

       // return view('admins.stock.categories.stats', compact('stats'));
    }
}