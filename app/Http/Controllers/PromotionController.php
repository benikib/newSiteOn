<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
           $validated = $request->validate([
        'service_id' => 'required|exists:services,id',
        'titre' => 'required|max:100',
        'description' => 'nullable',
        'prix' => 'required|numeric|min:0',
        'statut' => 'required|in:active,inactive,planned',
        'date_debut' => 'required|date',
        'date_fin' => 'required|date|after:date_debut'
    ]);

    Promotion::create($validated);


            return redirect()->back()->with('success', 'Promotion créée avec succès.');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Promotion $promotion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promotion $promotion)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promotion $promotion)
    {
        try {
            $request->validate([
                'titre' => 'required|string|max:100',
                'description' => 'nullable|string|max:255',
                'date_debut' => 'required|date',
                'date_fin' => 'required|date|after_or_equal:date_debut',
            ]);

            $promotion->update($request->all());

            return redirect()->back()->with('success', 'Promotion mise à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion)
    {
        try {
            $promotion->delete();
            return redirect()->back()->with('success', 'Promotion supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la suppression de la promotion.']);
        }
    }
}
