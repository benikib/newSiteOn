<?php

namespace App\Http\Controllers;

use App\Models\TauxDeChange;
use Illuminate\Http\Request;

class TauxController extends Controller
{
    public function update(Request $request, TauxDeChange $taux)
{

    $request->validate([
        'usd_cdf' => 'required|numeric|min:1',
        'date' => 'required|date|unique:taux_de_changes,date,' . $taux->id,
    ]);

    $taux->update($request->only(['usd_cdf', 'date']));

    return redirect()->back()->with('success', 'Taux mis à jour avec succès.');
}

}
