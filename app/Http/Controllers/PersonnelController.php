<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\Personnel;
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {


        // Assuming you want to filter by the authenticated user's etablissement
// Dans votre contrôleur

    $etablissementIds = Etablissement::whereHas('users', function ($q) {
        $q->where('users.id', auth()->id());
    })
    ->where('statut', 'actif')
    ->pluck('id');

    $personnels = Personnel::with(['equipes', 'etablissement'])
        ->whereIn('etablissement_id', $etablissementIds)
        ->orderBy('nom')
        ->paginate(15); // 15 éléments par page




        return view('etablissements.personnels.index', compact('personnels'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       try {
        $etablissementIds = Etablissement::whereHas('users', function ($q) {
        $q->where('users.id', auth()->id());
    })
    ->where('statut', 'actif')
    ->pluck('id');

     // Assuming you want to use the first etablissement_id from the filtered list

            $request->validate([
                'nom' => 'required|string|max:255',
                'telephone' => 'nullable|string|max:25',
                'poste' => 'nullable|string|max:100',
            ]);


            Personnel::create([
                'etablissement_id' => $etablissementIds->first(), // Use the first etablissement_id from the filtered list
                'nom' => $request->nom,
                'telephone' => $request->telephone,
                'poste' => $request->poste,
            ]);

            return redirect()->back()->with('success', 'Personnel créé avec succès.');
        } catch (\Exception $e) {

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Personnel $personnel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Personnel $personnel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Personnel $personnel)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:25',
            'poste' => 'nullable|string|max:100',
        ]);

        $personnel->update([
            'nom' => $request->nom,
            'telephone' => $request->telephone,
            'poste' => $request->poste,
        ]);

        return redirect()->back()->with('success', 'Personnel mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Personnel $personnel)
    {
        try {
            $personnel->delete();
            return redirect()->route('personnels.index')->with('success', 'Personnel supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la suppression du personnel : ' . $e->getMessage()]);
        }
    }
}
