<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Afficher la liste des produits
     */
    public function index(Request $request)
    {
        // ===== QUERY DE BASE =====
        $query = Product::query()
            ->with(['category', 'unit', 'etablissement']);

        // ===== FILTRES =====
        // Recherche par nom ou code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('code', 'LIKE', '%' . $search . '%')
                  ->orWhere('barcode', 'LIKE', '%' . $search . '%');
            });
        }

        // Filtre par catégorie
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtre par unité
        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        // Filtre par établissement
        if ($request->filled('etablissement_id')) {
            $query->where('etablissement_id', $request->etablissement_id);
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active' ? 1 : 0);
        }

        // ===== TRI =====
        switch ($request->sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'code_asc':
                $query->orderBy('code', 'asc');
                break;
            case 'code_desc':
                $query->orderBy('code', 'desc');
                break;
            case 'created_desc':
                $query->orderBy('created_at', 'desc');
                break;
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // ===== PAGINATION =====
        $perPage = $request->per_page ?? 15;
        $products = $query->paginate($perPage);

        // ===== STATISTIQUES =====
        $stats = [
            'total' => Product::count(),
            'active' => Product::where('status', true)->count(),
            'inactive' => Product::where('status', false)->count(),
            'total_categories' => Category::count(),
            'total_units' => Unit::count(),
            'total_etablissements' => Etablissement::count(),
        ];

        // ===== DONNÉES POUR LES FILTRES =====
        $categories = Category::where('status', true)->orderBy('nom')->get();
        $units = Unit::where('status', true)->orderBy('name')->get();
        $etablissements = Etablissement::orderBy('nom')->get();

        // ===== SI REQUÊTE AJAX =====
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $products,
                'stats' => $stats,
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ]);
        }

        // ===== VUE =====
        return view('admins.stock.products.index', compact('products', 'stats', 'categories', 'units', 'etablissements'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $categories = Category::where('status', true)->orderBy('nom')->get();
        $units = Unit::where('status', true)->orderBy('name')->get();
        $etablissements = Etablissement::orderBy('nom')->get();
        
        // Générer un code automatique
        $lastProduct = Product::orderBy('id', 'desc')->first();
        $nextId = $lastProduct ? $lastProduct->id + 1 : 1;
        $autoCode = 'PRD-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

        return view('admins.stock.products.create', compact('categories', 'units', 'etablissements', 'autoCode'));
    }

    /**
     * Enregistrer un nouveau produit
     */
    public function store(Request $request)
    {
        // ===== VALIDATION =====
        $validator = Validator::make($request->all(), [
            'etablissement_id' => 'required|exists:etablissements,id',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'code' => 'required|string|max:50|unique:products,code',
            'barcode' => 'nullable|string|max:50|unique:products,barcode',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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

        // ===== TRAITEMENT DE L'IMAGE =====
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $filename, 'public');
        }

        // ===== CRÉATION =====
        $product = Product::create([
            'etablissement_id' => $request->etablissement_id,
            'category_id' => $request->category_id,
            'unit_id' => $request->unit_id,
            'code' => $request->code,
            'barcode' => $request->barcode,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imagePath,
            'status' => $request->has('status') ? $request->status : true,
        ]);

        // ===== RÉPONSE =====
        $message = 'Produit "' . $product->name . '" créé avec succès !';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $product->load(['category', 'unit', 'etablissement'])
            ]);
        }

        return redirect()->route('stock.products.index')
            ->with('success', $message);
    }

    /**
     * Afficher un produit spécifique
     */
    public function show(Product $product)
    {
        $product->load(['category', 'unit', 'etablissement']);
        
        // Statistiques du produit
        $stats = [
            'stock_value' => $product->stock_value ?? 0,
            'quantity' => $product->quantity ?? 0,
            'alert_quantity' => $product->alert_quantity ?? 0,
        ];

        return view('admins.stock.products.show', compact('product', 'stats'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Product $product)
    {
        $categories = Category::where('status', true)->orderBy('nom')->get();
        $units = Unit::where('status', true)->orderBy('name')->get();
        $etablissements = Etablissement::orderBy('nom')->get();

        return view('admins.stock.products.edit', compact('product', 'categories', 'units', 'etablissements'));
    }

    /**
     * Mettre à jour un produit
     */
    public function update(Request $request, Product $product)
    {
        // ===== VALIDATION =====
        $validator = Validator::make($request->all(), [
            'etablissement_id' => 'required|exists:etablissements,id',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'code' => 'required|string|max:50|unique:products,code,' . $product->id,
            'barcode' => 'nullable|string|max:50|unique:products,barcode,' . $product->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
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

        // ===== TRAITEMENT DE L'IMAGE =====
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            
            $image = $request->file('image');
            $filename = time() . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $filename, 'public');
        } else {
            $imagePath = $product->image;
        }

        // ===== MISE À JOUR =====
        $product->update([
            'etablissement_id' => $request->etablissement_id,
            'category_id' => $request->category_id,
            'unit_id' => $request->unit_id,
            'code' => $request->code,
            'barcode' => $request->barcode,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imagePath,
            'status' => $request->has('status') ? $request->status : false,
        ]);

        // ===== RÉPONSE =====
        $message = 'Produit "' . $product->name . '" mis à jour avec succès !';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $product->load(['category', 'unit', 'etablissement'])
            ]);
        }

        return redirect()->route('stock.products.index')
            ->with('success', $message);
    }

    /**
     * Supprimer un produit
     */
    public function destroy(Request $request, Product $product)
    {
        // ===== SUPPRESSION DE L'IMAGE =====
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $productName = $product->name;
        $product->delete();

        // ===== RÉPONSE =====
        $message = 'Produit "' . $productName . '" supprimé avec succès !';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_id' => $product->id
            ]);
        }

        return redirect()->route('stock.products.index')
            ->with('success', $message);
    }

    /**
     * Activer/Désactiver un produit
     */
    public function toggleStatus(Request $request, Product $product)
    {
        $newStatus = !$product->status;
        $product->update(['status' => $newStatus]);

        $message = 'Produit "' . $product->name . '" ' . ($newStatus ? 'activé' : 'désactivé') . ' avec succès !';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $product
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Exporter les produits en CSV
     */
    public function export(Request $request)
    {
        $products = Product::with(['category', 'unit', 'etablissement'])->get();

        $filename = 'produits_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($products) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, [
                'ID', 'Code', 'Code-barres', 'Nom', 'Description', 
                'Établissement', 'Catégorie', 'Unité', 'Status', 'Créé le'
            ]);

            // Données
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->code,
                    $product->barcode ?? '',
                    $product->name,
                    $product->description ?? '',
                    $product->etablissement->nom ?? 'N/A',
                    $product->category->nom ?? 'Non catégorisé',
                    $product->unit->name ?? 'Non défini',
                    $product->status ? 'Actif' : 'Inactif',
                    $product->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Recherche rapide de produits
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2'
        ]);

        $products = Product::where('name', 'LIKE', '%' . $request->q . '%')
            ->orWhere('code', 'LIKE', '%' . $request->q . '%')
            ->orWhere('barcode', 'LIKE', '%' . $request->q . '%')
            ->with(['category', 'unit', 'etablissement'])
            ->limit(10)
            ->get(['id', 'name', 'code', 'status', 'etablissement_id']);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Obtenir les produits par établissement
     */
    public function getByEtablissement(Request $request)
    {
        $request->validate([
            'etablissement_id' => 'required|exists:etablissements,id'
        ]);

        $products = Product::where('etablissement_id', $request->etablissement_id)
            ->where('status', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}