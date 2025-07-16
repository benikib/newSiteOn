<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use Illuminate\Http\Request;
   
use App\Models\Reservation;

class ReservationController extends Controller
{



public function index()
{
    $etablissements = Etablissement::paginate(10);
    $reservations = Reservation::with('service')->latest()->paginate(10);

    return view('etablissements.reservation.index', compact('etablissements', 'reservations'));
}
public function changerStatut(Request $request, $id)
{
    $request->validate([
        'statut' => 'required|in:confirmé,rejeté'
    ]);

    $reservation = Reservation::findOrFail($id);
    $reservation->statut = $request->statut;
    $reservation->save();

    return response()->json(['message' => "Réservation mise à jour : {$request->statut}."]);
}

public function store(Request $request)
{
    $request->validate([
        'service_id' => 'required|integer|exists:services,id',
        'date' => 'required|date|after_or_equal:today',
        'client_name' => 'required|string|max:100',
        'client_phone' => 'nullable|string|max:25',
        'statut' => 'in:en_attente,confirme'
    ]);

    // Vérifier si la date est déjà réservée (optionnel)
  $existe = Reservation::where('service_id', $request->service_id)
    ->where('date', $request->date)
    ->where('statut', 'confirmé')
    ->exists();

if ($existe) {
    return response()->json(['message' => 'Cette date est déjà confirmée.'], 409);
}


    Reservation::create([
        'service_id' => $request->service_id,
        'client_name' => $request->client_name,
        'client_phone' => $request->client_phone,
        'statut' => $request->statut ?? 'en_attente', // valeur par défaut si non fourni
        'date' => $request->date
    ]);

    return response()->json(['message' => 'Réservation enregistrée avec succès.']);
}
 
}
