<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($etablissement)
    {

        $services = Service::where('etablissement_id', $etablissement)->get();
        $etablissement = Etablissement::findOrFail($etablissement);
        if (!$etablissement) {
            return redirect()->route('etablissements.index')->with('error', 'Établissement non trouvé.');
        }

        return view('admins.services.index', compact('services','etablissement'));
    }

      public function indexEtablissement($etablissement)
    {

        $services = Service::where('etablissement_id', $etablissement)->get();
        $etablissement = Etablissement::findOrFail($etablissement);
        if (!$etablissement) {
            return redirect()->route('etablissements.index')->with('error', 'Établissement non trouvé.');
        }

        return view('etablissements.services.index', compact('services','etablissement'));
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
                'description' => 'nullable|string|max:255',
                'etablissement_id' => 'required|exists:etablissements,id',
                'prix' => 'nullable|numeric|min:0',
            ]);

            Service::create([
                'nom' => $request->input('nom'),
                'description' => $request->input('description'),
                'etablissement_id' => $request->input('etablissement_id'),
                'prix' => $request->input('prix', null), // Default to null if not provided
            ]);

            return redirect()->back()
                             ->with('success', 'Service créé avec succès.');
            //return redirect()->route('services.index', ['etablissement' => $request->input('etablissement_id')])
        // ->with('success', 'Service créé avec succès.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        try {
            $service = Service::findOrFail($id);

            $request->validate([
                'nom' => 'required|string|max:100',
                'description' => 'nullable|string|max:255',
                'prix' => 'nullable|numeric|min:0',
            ]);

            $service->update($request->all());

            return  back()->with('success', 'Service ajouté avec succès');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        //
    }
}
