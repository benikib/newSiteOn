<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
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
     * Afficher la liste des catégories
     */
    public function index(Request $request)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        $query = Category::withCount('products');

        // Recherche
        if ($request->filled('search')) {
            $query->where('nom', 'LIKE', '%' . $request->search . '%');
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('status', $request->statut === 'actif');
        }

        $categories = $query->orderBy('nom')->paginate(12);

        return view('client.categories.index', compact('categories'));
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

        return view('client.categories.create');
    }

    /**
     * Enregistrer une nouvelle catégorie
     */
    public function store(Request $request)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:categories,nom',
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $category = Category::create([
            'nom' => $request->nom,
            'description' => $request->description,
            'status' => $request->boolean('status', true),
        ]);

        return redirect()->route('client.categories.index')
            ->with('success', 'Catégorie "' . $category->nom . '" créée avec succès !');
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit($id)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        $category = Category::findOrFail($id);

        return view('client.categories.edit', compact('category'));
    }

    /**
     * Mettre à jour une catégorie
     */
    public function update(Request $request, $id)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        $category = Category::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:categories,nom,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $category->update([
            'nom' => $request->nom,
            'description' => $request->description,
            'status' => $request->boolean('status'),
        ]);

        return redirect()->route('client.categories.index')
            ->with('success', 'Catégorie "' . $category->nom . '" mise à jour avec succès !');
    }

    /**
     * Supprimer une catégorie
     */
    public function destroy($id)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        $category = Category::findOrFail($id);

        // Vérifier si la catégorie contient des produits
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Cette catégorie contient des produits. Vous ne pouvez pas la supprimer.');
        }

        $categoryName = $category->nom;
        $category->delete();

        return redirect()->route('client.categories.index')
            ->with('success', 'Catégorie "' . $categoryName . '" supprimée avec succès !');
    }

    /**
     * Activer/Désactiver une catégorie
     */
    public function toggleStatus($id)
    {
        $etablissementIds = $this->getUserEtablissements();
        
        if ($etablissementIds->isEmpty()) {
            return redirect()->route('client.profile.edit')
                ->with('error', 'Vous n\'avez aucun établissement actif.');
        }

        $category = Category::findOrFail($id);
        $newStatus = !$category->status;
        $category->update(['status' => $newStatus]);

        return back()->with('success', 'Catégorie "' . $category->nom . '" ' . ($newStatus ? 'activée' : 'désactivée') . ' avec succès !');
    }
}