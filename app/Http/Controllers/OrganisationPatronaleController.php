<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicationRequest;
use App\Models\Cotisation;
use App\Models\Etablissement;
use App\Models\OrganisationPatronale;
use App\Models\PaiementCotisation;
use App\Models\Publication;
use App\Models\TypeEtablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\PublicationPublisher;

class OrganisationPatronaleController extends Controller
{
    public function dashboard(Request $request)
    {
        $organisation = $this->organisation($request);
        $etablissements = Etablissement::where('organisation_patronale_id', $organisation->id)
            ->with('typeEtablissement')
            ->orderBy('nom')
            ->get();
        $cotisations = $organisation->cotisations()
            ->with(['etablissement', 'paiements'])
            ->latest()
            ->get();
        $publications = $organisation->publications()->with('typesEtablissement')->latest()->get();
        $typesEtablissement = TypeEtablissement::orderBy('nom')->get();

        return view('organisations.dashboard', compact(
            'organisation', 'etablissements', 'cotisations', 'publications', 'typesEtablissement'
        ));
    }

    public function updateProfile(Request $request)
    {
        $organisation = $this->organisation($request);
        $data = $request->validate([
            'nom' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:25',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string|max:2000',
        ]);
        $organisation->update($data);

        return back()->with('success', 'Profil de l’organisation mis à jour.');
    }

    public function updateAffiliation(Request $request, Etablissement $etablissement)
    {
        $organisation = $this->organisation($request);
        abort_unless($etablissement->organisation_patronale_id === $organisation->id, 404);

        $data = $request->validate([
            'affiliation_statut' => 'required|in:approuvee,refusee',
        ]);
        $etablissement->update($data);

        return back()->with('success', 'Statut d’affiliation mis à jour.');
    }

    public function destroyAffiliation(Request $request, Etablissement $etablissement)
    {
        $organisation = $this->organisation($request);
        abort_unless($etablissement->organisation_patronale_id === $organisation->id, 404);
        $etablissement->update([
            'organisation_patronale_id' => null,
            'affiliation_statut' => 'aucune',
        ]);

        return back()->with('success', 'Établissement retiré des membres de l’organisation.');
    }

    public function storeCotisation(Request $request)
    {
        $organisation = $this->organisation($request);
        $data = $request->validate([
            'etablissement_id' => 'required|exists:etablissements,id',
            'libelle' => 'required|string|max:150',
            'description' => 'nullable|string|max:2000',
            'montant' => 'required|numeric|min:0.01',
            'devise' => 'required|in:CDF,USD',
            'date_echeance' => 'required|date',
        ]);
        $member = Etablissement::whereKey($data['etablissement_id'])
            ->where('organisation_patronale_id', $organisation->id)
            ->where('affiliation_statut', 'approuvee')
            ->exists();
        abort_unless($member, 404);

        $organisation->cotisations()->create($data);

        return back()->with('success', 'Appel de cotisation enregistré.');
    }

    public function storePaiement(Request $request, Cotisation $cotisation)
    {
        $organisation = $this->organisation($request);
        abort_unless($cotisation->organisation_patronale_id === $organisation->id, 404);
        $data = $request->validate([
            'montant' => 'required|numeric|min:0.01',
            'date_paiement' => 'required|date',
            'mode' => 'nullable|string|max:40',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
        ]);

        DB::transaction(function () use ($cotisation, $data, $request) {
            $locked = Cotisation::whereKey($cotisation->id)->lockForUpdate()->firstOrFail();
            $paye = (float) $locked->paiements()->sum('montant');
            $restant = (float) $locked->montant - $paye;
            abort_if((float) $data['montant'] > $restant, 422, 'Le paiement dépasse le solde de la cotisation.');

            $locked->paiements()->create($data + ['enregistre_par' => $request->user()->id]);
            $totalPaye = (float) $locked->paiements()->sum('montant');
            $locked->update(['statut' => $totalPaye >= (float) $locked->montant ? 'payee' : 'partielle']);
        });

        return back()->with('success', 'Paiement de cotisation enregistré.');
    }

    public function storePublication(PublicationRequest $request, PublicationPublisher $publisher)
    {
        $organisation = $this->organisation($request);
        $publisher->publish($request->user(), $request->validated(), $organisation->id);

        return back()->with('success', 'Publication enregistrée.');
    }

    private function organisation(Request $request): OrganisationPatronale
    {
        return $request->user()->organisationPatronale()->firstOrFail();
    }
}
