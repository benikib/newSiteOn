<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $units = Unit::all();
        return view('admins.stock.unites.index', compact('units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.stock.unites.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        Unit::create($request->only(['name', 'symbol', 'description', 'status']));

        return redirect()->route('units.index')->with('success', 'Unité créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        return view('admins.stock.unites.show', compact('unit'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        return view('admins.stock.unites.edit', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $unit->update($request->only(['name', 'symbol', 'description', 'status']));

        return redirect()->route('units.index')->with('success', 'Unité mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()->route('units.index')->with('success', 'Unité supprimée avec succès.');
    }
}
