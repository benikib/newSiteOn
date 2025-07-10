<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\Publicite;
use Illuminate\Http\Request;

class PubliciteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $publicites = Publicite::all();
        $etablissements = Etablissement::all();

        return view('admins.publicites.index', compact('publicites', 'etablissements'));
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

         $validated =  $request->validate([
                'titre' => 'required|string|max:100',
                'description' => 'nullable|string|max:255',
                'date' => 'required|date',
                'etablissement_id' => 'required|exists:etablissements,id',
                'dure' => 'required|integer|min:1',
                'image' => 'required',
                'status'    => 'required|', // Ajout de la validation pour le statut
            ]);
             $path = $request->file('image')->store('photos', 'public');

    // Enregistrez le chemin RELATIF sans 'public/'
    $validated['image_path'] = $path; // 'photos/filename.jpg'

    // Solution 2 - Si vous préférez garder l'ancienne structure
    // $path = $request->file('image')->store('photos'); // Sans 'public/'
    // $validated['image_path'] = $path;


            Publicite::create($validated);

            return redirect()->back()->with('success', 'Publicité créée avec succès.');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Publicite $publicite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Publicite $publicite)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)

    {

        try {
            $publicite = Publicite::findOrFail($id);

            $request->validate([
                'titre' => 'required|string|max:100',
                'description' => 'nullable|string|max:255',
                'date' => 'required|date',
                'etablissement_id' => 'required|exists:etablissements,id',
                'dure' => 'required|integer|min:1',
                'status' => 'required|in:active,inactive,planned', // Validation pour le statut
            ]);


            $publicite->update($request->all());

            return redirect()->back()->with('success', 'Publicité mise à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publicite $publicite)
    {
        //
    }
}
