<?php

namespace App\Http\Controllers;

use App\Models\Equipe;
use App\Models\Etablissement;
use App\Models\Personnel;
use App\Models\Reservation;
use App\Models\Service;
use Illuminate\Http\Request;

class EquipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $etablissementIds = Etablissement::whereHas('users', function ($q) {
        $q->where('users.id', auth()->id());
    })
    ->where('statut', 'actif')
    ->pluck('id');

    $serviceIds = Service::whereIn('etablissement_id', $etablissementIds)->pluck('id');

Reservation::whereIn('service_id', $serviceIds)
    ->where('created_at', '<', now()->subHours(48))
    ->delete();

    $reservations = Reservation::with(['service', 'service.etablissement'])
    ->whereIn('service_id', $serviceIds)
    ->where('created_at', '>=', now()->subHours(48)) // garde seulement les < 48h
    ->latest()
    ->paginate(10);
          $personnels = Personnel::where('etablissement_id', $etablissementIds->first())->get();
          $equipes = Equipe::with(['personnels', 'etablissement'])
            ->where('etablissement_id', $etablissementIds->first())
            ->orderBy('nom')
            ->paginate(15); // 15 éléments par page



    return view('etablissements.equipes.index', compact('personnels', 'equipes','reservations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    $personnels = Personnel::where('etablissement_id', auth()->user()->etablissement_id)->get();
    return view('equipes.create', compact('personnels'));
}

public function store(Request $request)
{
    try {
        $etablissementIds = Etablissement::whereHas('users', function ($q) {
            $q->where('users.id', auth()->id());
        })
        ->where('statut', 'actif')
        ->pluck('id');

        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'personnels' => 'array',
            'personnels.*' => 'exists:personnels,id',
        ]);


        $equipe = Equipe::create([
            'etablissement_id' => $etablissementIds->first(),
            'evenement' => $request->evenement ?? null,
            'nom' => $request->nom,
            'description' => $request->description,
        ]);


        if ($request->has('personnels')) {
            $equipe->personnels()->sync($request->personnels);

        }

        return redirect()->back()->with('success', 'Équipe créée avec succès.');
    } catch (\Exception $e) {
        dd($e->getMessage());
        return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(Equipe $equipe)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipe $equipe)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Equipe $equipe)
    {
        try {

    $request->validate([
        'nom' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'personnels' => 'array',
        'personnels.*' => 'exists:personnels,id',
    ]);

    $equipe->update([
        'nom' => $request->nom,
        'evenement' => $request->evenement ?? null,
        'description' => $request->description,
    ]);
    if ($request->has('personnels')) {
        $equipe->personnels()->sync($request->personnels);
    } else {
        $equipe->personnels()->detach();
    }
    return redirect()->back()->with('success', 'Équipe mise à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la mise à jour de l\'équipe : ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipe $equipe)
    {
    try {
        $equipe->delete();
        return redirect()->back()->with('success', 'Équipe supprimée avec succès.');
    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['error' => 'Erreur lors de la suppression de l\'équipe : ' . $e->getMessage()]);
    }
}
}
