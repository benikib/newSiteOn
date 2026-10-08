@extends('layouts.base')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
        <div>
            <a href="{{ route('etablissements.index') }}" class="text-decoration-none small">← Établissements</a>
            <h1 class="h4 mb-1 mt-2">Historique des abonnements</h1>
            <div class="text-muted">{{ $etablissement->nom }}</div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.abonnements.print', $etablissement) }}" target="_blank">
                <i class="fas fa-print me-1"></i> Imprimer
            </a>
            <a class="btn btn-outline-danger btn-sm" href="{{ route('admin.abonnements.pdf', $etablissement) }}">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <section class="border-bottom pb-4 mb-4">
        <h2 class="h6 mb-3">Abonnement en cours</h2>
        @if($current)
            <div class="d-flex flex-wrap align-items-center gap-3">
                <span class="badge bg-success">Actif</span>
                <span>{{ $current->date_debut->format('d/m/Y') }} au {{ $current->date_fin->format('d/m/Y') }}</span>
                <strong class="{{ $daysRemaining < 7 ? 'text-danger' : '' }}">
                    {{ $daysRemaining === 0 ? 'Expire aujourd’hui' : $daysRemaining . ' jour(s) restant(s)' }}
                </strong>
            </div>
            @if($daysRemaining >= 0 && $daysRemaining < 7)
                <div class="alert alert-warning mt-3 mb-0" role="alert">
                    Cet abonnement expire dans moins de 7 jours.
                </div>
            @endif
        @else
            <p class="text-muted mb-0">Aucun abonnement ne couvre la date du jour.</p>
        @endif
    </section>

    <section class="border-bottom pb-4 mb-4">
        <h2 class="h6 mb-3">Ajouter une période</h2>
        <form method="POST" action="{{ route('admin.abonnements.store', $etablissement) }}" class="row g-3">
            @csrf
            <div class="col-md-4">
                <label for="type_operation" class="form-label">Opération</label>
                <select class="form-select" id="type_operation" name="type_operation" required>
                    @if($current)
                        <option value="renouvellement" {{ $defaultOperation === 'renouvellement' ? 'selected' : '' }}>Renouvellement</option>
                        <option value="prolongation">Prolongation</option>
                    @elseif($hasHistory)
                        <option value="reactivation_manuelle" selected>Réactivation manuelle</option>
                    @else
                        <option value="creation" selected>Création</option>
                    @endif
                </select>
            </div>
            <div class="col-md-4">
                <label for="date_debut" class="form-label">Date de début</label>
                <input class="form-control" id="date_debut" name="date_debut" type="date"
                    value="{{ old('date_debut', $defaultStartDate) }}" {{ $current ? 'readonly' : '' }} required>
                @if($current)<small class="text-muted">Prochain jour après la fin de la période en cours.</small>@endif
            </div>
            <div class="col-md-4">
                <label for="date_fin" class="form-label">Date de fin</label>
                <input class="form-control" id="date_fin" name="date_fin" type="date"
                    min="{{ $defaultStartDate }}" value="{{ old('date_fin') }}" required>
            </div>
            <div class="col-md-6">
                <label for="motif" class="form-label">Motif</label>
                <textarea class="form-control" id="motif" name="motif" rows="2" maxlength="2000" required>{{ old('motif') }}</textarea>
            </div>
            <div class="col-md-3">
                <label for="montant_paye" class="form-label">Montant payé (optionnel)</label>
                <input class="form-control" id="montant_paye" name="montant_paye" type="number" min="0" step="0.01" value="{{ old('montant_paye') }}">
            </div>
            <div class="col-md-3">
                <label for="devise" class="form-label">Devise</label>
                <select class="form-select" id="devise" name="devise">
                    <option value="">Non renseignée</option>
                    <option value="CDF">CDF</option>
                    <option value="USD">USD</option>
                </select>
            </div>
            <div class="col-12">
                <button class="btn btn-primary" type="submit"><i class="fas fa-plus me-1"></i> Enregistrer dans l’historique</button>
            </div>
        </form>
    </section>

    @if($current)
        <section class="border-bottom pb-4 mb-4">
            <h2 class="h6 mb-3">Suspendre l’établissement</h2>
            <form method="POST" action="{{ route('admin.abonnements.suspend', $etablissement) }}" class="row g-2 align-items-end">
                @csrf
                <div class="col-md-9">
                    <label for="suspension_motif" class="form-label">Motif de suspension</label>
                    <input class="form-control" id="suspension_motif" name="motif" maxlength="2000" required>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-danger" type="submit">Suspendre et tracer</button>
                </div>
            </form>
            <small class="text-muted">La suspension est une entrée d’historique distincte; l’abonnement original est conservé.</small>
        </section>
    @endif

    <section>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <h2 class="h6 mb-0">Historique chronologique</h2>
            <form method="GET" class="d-flex flex-wrap gap-2">
                <select class="form-select form-select-sm" name="statut" aria-label="Filtrer par statut">
                    <option value="">Tous les statuts</option>
                    @foreach(['actif' => 'Actif', 'expire' => 'Expiré', 'annule' => 'Annulé', 'remplace' => 'Remplacé'] as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['statut'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <input class="form-control form-control-sm" type="date" name="date_debut" aria-label="Période à partir du" value="{{ $filters['date_debut'] ?? '' }}">
                <input class="form-control form-control-sm" type="date" name="date_fin" aria-label="Période jusqu’au" value="{{ $filters['date_fin'] ?? '' }}">
                <button class="btn btn-sm btn-outline-primary" type="submit">Filtrer</button>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.abonnements.index', $etablissement) }}">Effacer</a>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead><tr><th>Créé le</th><th>Période</th><th>Opération</th><th>Statut</th><th>Motif</th><th>Paiement</th><th>Créé par</th></tr></thead>
                <tbody>
                    @forelse($abonnements as $abonnement)
                        <tr>
                            <td>{{ $abonnement->created_at?->format('d/m/Y H:i') }}</td>
                            <td>{{ $abonnement->date_debut->format('d/m/Y') }} – {{ $abonnement->date_fin->format('d/m/Y') }}</td>
                            <td>{{ $abonnement->type_operation_label }}</td>
                            <td>
                                <span class="badge {{ $abonnement->statut === 'actif' ? 'bg-success' : ($abonnement->statut === 'expire' ? 'bg-secondary' : 'bg-warning text-dark') }}">
                                    {{ $abonnement->statut_label }}
                                </span>
                            </td>
                            <td>{{ $abonnement->motif }}</td>
                            <td>{{ $abonnement->montant_paye !== null ? number_format($abonnement->montant_paye, 2, ',', ' ') . ' ' . $abonnement->devise : '—' }}</td>
                            <td>{{ $abonnement->createur->name ?? 'Système' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">Aucun abonnement pour ces filtres.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $abonnements->links() }}
    </section>
</div>
@endsection