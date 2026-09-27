<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Etablissement;
use App\Models\Stock;
use App\Models\Movement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
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
     * Vérifier si l'utilisateur a accès à un établissement
     */
    private function hasEtablissementAccess($etablissementId)
    {
        return Etablissement::whereHas('users', function ($q) {
            $q->where('users.id', auth()->id());
        })
        ->where('id', $etablissementId)
        ->where('statut', 'actif')
        ->exists();
    }

    /**
     * Afficher la liste des produits
     */
    public function index(Request $request)
    {
        // Récupérer les établissements de l'utilisateur
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        // Sélectionner un établissement
        $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();
        
        if (!$etablissementIds->contains($selectedEtablissementId)) {
            $selectedEtablissementId = $etablissementIds->first();
        }

        // ===== QUERY DE BASE =====
        $query = Product::with(['category', 'unit', 'etablissement', 'stock'])
            ->where('etablissement_id', $selectedEtablissementId);

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
            case 'stock_asc':
                $query->orderBy(Stock::select('quantity')->whereColumn('stock.product_id', 'products.id'), 'asc');
                break;
            case 'stock_desc':
                $query->orderBy(Stock::select('quantity')->whereColumn('stock.product_id', 'products.id'), 'desc');
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

        // ===== DONNÉES POUR LES FILTRES =====
        $categories = Category::where('status', true)->orderBy('nom')->get();
        $units = Unit::where('status', true)->orderBy('name')->get();
        $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();

        // ===== SI REQUÊTE AJAX =====
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $products,
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ]);
        }

        return view('client.products.index', compact(
            'products',
            'categories',
            'units',
            'etablissements',
            'selectedEtablissementId'
        ));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create(Request $request)
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

        $categories = Category::where('status', true)->orderBy('nom')->get();
        $units = Unit::where('status', true)->orderBy('name')->get();
        $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();
        $etablissement = $etablissements->firstWhere('id', $selectedEtablissementId);

        // Générer un code automatique
        $lastProduct = Product::where('etablissement_id', $selectedEtablissementId)
            ->orderBy('id', 'desc')
            ->first();
        $nextId = $lastProduct ? $lastProduct->id + 1 : 1;
        $autoCode = 'PRD-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

        return view('client.products.create', compact(
            'categories',
            'units',
            'etablissements',
            'etablissement',
            'selectedEtablissementId',
            'autoCode'
        ));
    }

    /**
     * Enregistrer un nouveau produit
     */
    public function store(Request $request)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        // ===== VALIDATION =====
        $validator = Validator::make($request->all(), [
            'etablissement_id' => 'required|in:' . $etablissementIds->implode(','),
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'code' => 'required|string|max:50|unique:products,code,NULL,id,etablissement_id,' . $request->etablissement_id,
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

        DB::beginTransaction();
        try {
            // ===== CRÉATION DU PRODUIT =====
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

            // ===== CRÉATION DU STOCK INITIAL =====
            $stock = Stock::create([
                'etablissement_id' => $request->etablissement_id,
                'product_id' => $product->id,
                'quantity' => 0,
                'purchase_price' => 0,
                'selling_price' => 0,
                'minimum_stock' => 0,
            ]);

            DB::commit();

            $message = 'Produit "' . $product->name . '" créé avec succès !';

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $product->load(['category', 'unit', 'etablissement'])
                ]);
            }

            return redirect()->route('client.products.index', ['etablissement_id' => $request->etablissement_id])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Supprimer l'image si erreur
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Request $request, $id)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        $product = Product::with(['category', 'unit', 'etablissement', 'stock'])
            ->whereIn('etablissement_id', $etablissementIds)
            ->findOrFail($id);

        $categories = Category::where('status', true)->orderBy('nom')->get();
        $units = Unit::where('status', true)->orderBy('name')->get();
        $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();

        return view('client.products.edit', compact(
            'product',
            'categories',
            'units',
            'etablissements'
        ));
    }

    /**
     * Mettre à jour un produit
     */
    public function update(Request $request, $id)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        $product = Product::whereIn('etablissement_id', $etablissementIds)
            ->findOrFail($id);

        // ===== VALIDATION =====
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'code' => 'required|string|max:50|unique:products,code,' . $product->id . ',id,etablissement_id,' . $product->etablissement_id,
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
        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            
            $image = $request->file('image');
            $filename = time() . '_' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $filename, 'public');
        }

        // ===== MISE À JOUR =====
        $product->update([
            'category_id' => $request->category_id,
            'unit_id' => $request->unit_id,
            'code' => $request->code,
            'barcode' => $request->barcode,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imagePath,
            'status' => $request->has('status') ? $request->status : false,
        ]);

        $message = 'Produit "' . $product->name . '" mis à jour avec succès !';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $product->load(['category', 'unit', 'etablissement'])
            ]);
        }

        return redirect()->route('client.products.index', ['etablissement_id' => $product->etablissement_id])
            ->with('success', $message);
    }

    /**
     * Supprimer un produit
     */
    public function destroy(Request $request, $id)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'avez aucun établissement actif.'
                ], 403);
            }
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        $product = Product::whereIn('etablissement_id', $etablissementIds)
            ->findOrFail($id);

        // Vérifier si le produit a des mouvements
        $hasMovements = Movement::where('product_id', $product->id)
            ->whereIn('etablissement_id', $etablissementIds)
            ->exists();

        if ($hasMovements) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce produit a des mouvements associés. Vous ne pouvez pas le supprimer.'
                ], 422);
            }
            return back()->with('error', 'Ce produit a des mouvements associés. Vous ne pouvez pas le supprimer.');
        }

        // ===== SUPPRESSION DE L'IMAGE =====
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        // ===== SUPPRESSION DU STOCK =====
        $stock = Stock::where('product_id', $product->id)
            ->where('etablissement_id', $product->etablissement_id)
            ->first();
        if ($stock) {
            $stock->delete();
        }

        $productName = $product->name;
        $etablissementId = $product->etablissement_id;
        $product->delete();

        $message = 'Produit "' . $productName . '" supprimé avec succès !';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_id' => $product->id
            ]);
        }

        return redirect()->route('client.products.index', ['etablissement_id' => $etablissementId])
            ->with('success', $message);
    }

    /**
     * Activer/Désactiver un produit
     */
    public function toggleStatus(Request $request, $id)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'avez aucun établissement actif.'
                ], 403);
            }
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        $product = Product::whereIn('etablissement_id', $etablissementIds)
            ->findOrFail($id);

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
     * Recherche rapide de produits
     */
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2'
        ]);

        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return response()->json([
                'success' => false,
                'data' => []
            ]);
        }

        $products = Product::whereIn('etablissement_id', $etablissementIds)
            ->where(function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%')
                  ->orWhere('code', 'LIKE', '%' . $request->q . '%')
                  ->orWhere('barcode', 'LIKE', '%' . $request->q . '%');
            })
            ->with(['category', 'unit', 'stock'])
            ->limit(10)
            ->get(['id', 'name', 'code', 'status', 'category_id', 'unit_id']);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Exporter les produits en CSV
     */
    public function export(Request $request)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();

        $products = Product::with(['category', 'unit', 'stock'])
            ->where('etablissement_id', $selectedEtablissementId)
            ->get();

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
                'Catégorie', 'Unité', 'Quantité en stock', 'Statut', 'Créé le'
            ]);

            // Données
            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->code,
                    $product->barcode ?? '',
                    $product->name,
                    $product->description ?? '',
                    $product->category->nom ?? 'N/A',
                    $product->unit->name ?? 'N/A',
                    $product->stock->quantity ?? 0,
                    $product->status ? 'Actif' : 'Inactif',
                    $product->created_at->format('d/m/Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Obtenir les produits par établissement (API)
     */
    public function getByEtablissement(Request $request)
    {
        $request->validate([
            'etablissement_id' => 'required|exists:etablissements,id'
        ]);

        $etablissementIds = $this->getUserEtablissements();
        
        if (!$etablissementIds->contains($request->etablissement_id)) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé à cet établissement.'
            ], 403);
        }

        $products = Product::where('etablissement_id', $request->etablissement_id)
            ->where('status', true)
            ->with(['stock', 'unit'])
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}