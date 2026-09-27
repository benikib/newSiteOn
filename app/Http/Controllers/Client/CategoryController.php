<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

        $selectedEtablissementId = $request->etablissement_id ?? $etablissementIds->first();

        $query = Category::withCount('products')
            ->where('etablissement_id', $selectedEtablissementId);

        // Recherche
        if ($request->filled('search')) {
            $query->where('nom', 'LIKE', '%' . $request->search . '%');
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut === 'actif' ? 1 : 0);
        }

        $categories = $query->orderBy('nom')->paginate(12);

        $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();

        return view('client.categories.index', compact(
            'categories',
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
        $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();

        return view('client.categories.create', compact(
            'etablissements',
            'selectedEtablissementId'
        ));
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
            'etablissement_id' => 'required|in:' . $etablissementIds->implode(','),
            'nom' => 'required|string|max:255|unique:categories,nom',
            'description' => 'nullable|string|max:1000',
            'statut' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $category = Category::create([
            'etablissement_id' => $request->etablissement_id,
            'nom' => $request->nom,
            'description' => $request->description,
            'statut' => $request->has('statut') ? $request->statut : true,
        ]);

        return redirect()->route('client.categories.index', ['etablissement_id' => $request->etablissement_id])
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

        $category = Category::whereIn('etablissement_id', $etablissementIds)->findOrFail($id);
        $etablissements = Etablissement::whereIn('id', $etablissementIds)->get();

        return view('client.categories.edit', compact('category', 'etablissements'));
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

        $category = Category::whereIn('etablissement_id', $etablissementIds)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255|unique:categories,nom,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'statut' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $category->update([
            'nom' => $request->nom,
            'description' => $request->description,
            'statut' => $request->has('statut') ? $request->statut : false,
        ]);

        return redirect()->route('client.categories.index', ['etablissement_id' => $category->etablissement_id])
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

        $category = Category::whereIn('etablissement_id', $etablissementIds)->findOrFail($id);

        // Vérifier si la catégorie contient des produits
        if ($category->products()->count() > 0) {
            return back()->with('error', 'Cette catégorie contient des produits. Vous ne pouvez pas la supprimer.');
        }

        $categoryName = $category->nom;
        $etablissementId = $category->etablissement_id;
        $category->delete();

        return redirect()->route('client.categories.index', ['etablissement_id' => $etablissementId])
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

        $category = Category::whereIn('etablissement_id', $etablissementIds)->findOrFail($id);
        $newStatus = !$category->statut;
        $category->update(['statut' => $newStatus]);

        return back()->with('success', 'Catégorie "' . $category->nom . '" ' . ($newStatus ? 'activée' : 'désactivée') . ' avec succès !');
    }
}