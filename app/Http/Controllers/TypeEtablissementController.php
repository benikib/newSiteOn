<?php

namespace App\Http\Controllers;

use App\Models\TypeEtablissement;
use Illuminate\Http\Request;

class TypeEtablissementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $typeEtablissements = TypeEtablissement::all();
        return view('admins.type_etablissements.index', compact('typeEtablissements'));
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
             $request->validate([
                'nom' => 'required|string|max:100',
                'description' => 'required|string|max:255',
            ]);

            TypeEtablissement::create([
                'nom' => $request->input('nom'),
                'description' => $request->input('description'),
            ]);
            return redirect()->route('type_etablissements.index')->with('success', 'Type d\'établissement créé avec succès.');


        } catch (\Illuminate\Validation\ValidationException $e) {

            return redirect()->back()->withErrors($e->validator)->withInput();
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(TypeEtablissement $typeEtablissement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeEtablissement $typeEtablissement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        try {

            $typeEtablissement = TypeEtablissement::findOrFail($id);

            $request->validate([
                'nom' => 'required|string|max:100',
                'description' => 'required|string|max:255',
            ]);

            $typeEtablissement->update([
                'nom' => $request->input('nom'),
                'description' => $request->input('description'),
            ]);

            return redirect()->route('type_etablissements.index')->with('success', 'Type d\'établissement mis à jour avec succès.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TypeEtablissement $typeEtablissement)
    {
        try {
            $typeEtablissement->delete();
            return redirect()->route('type_etablissements.index')->with('success', 'Type d\'établissement supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression du type d\'établissement.');
        }
    }
}
