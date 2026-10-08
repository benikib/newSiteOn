<?php

namespace App\Http\Controllers;

use App\Models\Abonnement;
use App\Models\Etablissement;
use App\Services\AbonnementService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class AbonnementController extends Controller
{
    public function index(Request $request, Etablissement $etablissement, AbonnementService $service)
    {
        $service->refreshEtablissement($etablissement);

        $filters = $request->validate([
            'statut' => 'nullable|in:actif,expire,annule,remplace',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
        ]);

        $query = $etablissement->abonnements()->with('createur');
        if (!empty($filters['statut'])) {
            $query->where('statut', $filters['statut']);
        }
        if (!empty($filters['date_debut'])) {
            $query->whereDate('date_fin', '>=', $filters['date_debut']);
        }
        if (!empty($filters['date_fin'])) {
            $query->whereDate('date_debut', '<=', $filters['date_fin']);
        }

        $current = $service->currentPeriod($etablissement);
        $daysRemaining = $current
            ? CarbonImmutable::today()->diffInDays(CarbonImmutable::parse($current->date_fin), false)
            : null;
        $hasHistory = $etablissement->abonnements()->exists();
        $defaultOperation = $current ? 'renouvellement' : ($hasHistory ? 'reactivation_manuelle' : 'creation');
        $defaultStartDate = $current
            ? CarbonImmutable::parse($current->date_fin)->addDay()->toDateString()
            : CarbonImmutable::today()->toDateString();

        return view('admins.abonnements.index', [
            'etablissement' => $etablissement,
            'abonnements' => $query->paginate(20)->withQueryString(),
            'current' => $current,
            'daysRemaining' => $daysRemaining,
            'defaultOperation' => $defaultOperation,
            'defaultStartDate' => $defaultStartDate,
            'hasHistory' => $hasHistory,
            'filters' => $filters,
        ]);
    }

    public function store(Request $request, Etablissement $etablissement, AbonnementService $service)
    {
        $data = $request->validate([
            'type_operation' => 'required|in:creation,renouvellement,prolongation,reactivation_manuelle',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'required|string|max:2000',
            'montant_paye' => 'nullable|numeric|min:0|required_with:devise',
            'devise' => 'nullable|in:CDF,USD|required_with:montant_paye',
        ]);

        $service->create($etablissement, $data, $request->user());

        return redirect()->route('admin.abonnements.index', $etablissement)
            ->with('success', 'Nouvel abonnement ajouté à l’historique.');
    }

    public function suspend(Request $request, Etablissement $etablissement, AbonnementService $service)
    {
        $data = $request->validate([
            'motif' => 'required|string|max:2000',
        ]);

        $service->suspend($etablissement, $data['motif'], $request->user());

        return redirect()->route('admin.abonnements.index', $etablissement)
            ->with('success', 'Suspension enregistrée dans l’historique.');
    }

    public function print(Etablissement $etablissement)
    {
        return view('admins.abonnements.export', [
            'etablissement' => $etablissement,
            'abonnements' => $etablissement->abonnements()->with('createur')->get(),
            'printButton' => true,
        ]);
    }

    public function pdf(Etablissement $etablissement)
    {
        $pdf = Pdf::loadView('admins.abonnements.export', [
            'etablissement' => $etablissement,
            'abonnements' => $etablissement->abonnements()->with('createur')->get(),
            'printButton' => false,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('historique-abonnements-' . $etablissement->id . '.pdf');
    }
}