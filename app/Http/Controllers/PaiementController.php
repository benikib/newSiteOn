<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\Paiement;
use App\Models\Service;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $etablissements = Etablissement::whereHas('users', function($query) {
        $query->where('user_id', auth()->id());
    })->with(['photos', 'services'])->paginate(5);
    $etablissementIds = Etablissement::whereHas('users', function ($q) {
        $q->where('users.id', auth()->id());
    })
    ->where('statut', '=', 'actif')
    ->pluck('id');

        $services = Service::where('etablissement_id', $etablissementIds)->get();



$serviceIds = Service::whereIn('etablissement_id', $etablissementIds)->pluck('id');
        $paiements = Paiement::with(['service', 'service.etablissement'])
            ->whereIn('service_id', $serviceIds)
            ->latest()
            ->paginate(10);
        return view('etablissements.paiements.index', compact('paiements' ,'services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Logic to show the form for creating a new payment
        return view('paiements.create');
    }
    public function paiementReservation(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'service_id' => 'required|exists:services,id',
            'client' => 'required|string|max:100',
            'client_phone' => 'nullable|string|max:25',
            'montant' => 'required|numeric|min:0',
            'date' => 'nullable|date'
        ]);

        Paiement::create($request->all());

        return redirect()->back()->with('success', 'Paiement effectué avec succès.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
 try {
                $request->validate([
            'service_id' => 'required|exists:services,id',
            'client' => 'required|string|max:100',
            'client_phone' => 'nullable|string|max:25',
            'montant' => 'required|numeric|min:0',
            'date' => 'nullable|date'
        ]);

         Paiement::create($request->all());

        return redirect()->back()->with( 'success', 'Paiement créé avec succès.');

      } catch (\Throwable $th) {
        dd($th->getMessage());

      }

    }

    /**
     * Display the specified resource.
     */
    public function show(Paiement $paiement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Paiement $paiement)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Paiement $paiement)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'client' => 'required|string|max:100',
            'client_phone' => 'nullable|string|max:25',
            'montant' => 'required|numeric|min:0',
            'date' => 'nullable|date'
        ]);

        $paiement->update($request->all());

        return redirect()->route('paiements.index')->with('success', 'Paiement mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Paiement $paiement)
    {
        $paiement->delete();

        return redirect()->route('paiements.index')->with('success', 'Paiement supprimé avec succès.');
    }
}
