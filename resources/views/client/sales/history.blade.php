@extends('layouts.main')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- ===== EN-TÊTE ===== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-history me-2 text-info"></i> Historique des ventes
            </h4>
            <p class="text-muted mb-0 small">
                Consultez toutes vos ventes en physique
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('client.sales.pos') }}" class="btn btn-success btn-sm">
                <i class="fas fa-cash-register me-1"></i> Nouvelle vente
            </a>
        </div>
    </div>

    <!-- ===== SÉLECTEUR D'ÉTABLISSEMENT ===== -->
    @if(isset($etablissements) && $etablissements->count() > 1)
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('client.sales.history') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Établissement</label>
                        <select name="etablissement_id" class="form-select" onchange="this.form.submit()">
                            @foreach($etablissements as $etab)
                                <option value="{{ $etab->id }}" {{ ($selectedEtablissementId ?? $etab->id) == $etab->id ? 'selected' : '' }}>
                                    {{ $etab->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ===== FILTRES ===== -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('client.sales.history') }}" method="GET" class="row g-3">
                @if(isset($selectedEtablissementId))
                    <input type="hidden" name="etablissement_id" value="{{ $selectedEtablissementId }}">
                @endif
                
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="N° commande..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <input type="date" 
                           name="date_from" 
                           class="form-control" 
                           placeholder="Date début"
                           value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" 
                           name="date_to" 
                           class="form-control" 
                           placeholder="Date fin"
                           value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== STATISTIQUES RAPIDES ===== -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-left-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total ventes</span>
                            <h3 class="mb-0">{{ $orders->total() }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-receipt text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-left-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Montant total</span>
                            <h5 class="mb-0 text-success">
                                {{ number_format($orders->sum('total_amount'), 2, ',', ' ') }} Fc
                            </h5>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-euro-sign text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-left-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Moyenne/vente</span>
                            <h5 class="mb-0 text-info">
                                {{ number_format($orders->avg('total_amount') ?? 0, 2, ',', ' ') }} Fc
                            </h5>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-chart-line text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-left-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Aujourd'hui</span>
                            <h5 class="mb-0 text-warning">
                                {{ number_format($orders->where('created_at', '>=', today())->sum('total_amount'), 2, ',', ' ') }} Fc
                            </h5>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-calendar-day text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== LISTE DES VENTES ===== -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>N° Commande</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Total HT</th>
                            <th>TVA</th>
                            <th>Total TTC</th>
                            <th>Paiement</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr>
                                <td>
                                    <span class="fw-bold">{{ $order->order_number }}</span>
                                </td>
                                <td>
                                    <small>{{ $order->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    {{ $order->customer_name ?? 'Client physique' }}
                                    @if($order->customer_phone)
                                        <br>
                                        <small class="text-muted">{{ $order->customer_phone }}</small>
                                    @endif
                                </td>
                                <td>
                                    {{ number_format($order->total_ht, 2, ',', ' ') }} Fc
                                </td>
                                <td>
                                    {{ number_format($order->total_tva, 2, ',', ' ') }} Fc
                                </td>
                                <td class="fw-bold">
                                    {{ number_format($order->total_amount, 2, ',', ' ') }} Fc
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $order->payment_method_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $order->status_badge }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('client.sales.invoice', $order->id) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="Voir facture"
                                           target="_blank">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                        <a href="{{ route('client.sales.pdf', $order->id) }}" 
                                           class="btn btn-sm btn-outline-danger" 
                                           title="Télécharger PDF"
                                           target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <a href="{{ route('client.sales.print', $order->id) }}" 
                                           class="btn btn-sm btn-outline-secondary" 
                                           title="Imprimer"
                                           target="_blank">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-receipt fa-3x text-muted mb-3 d-block"></i>
                                    <h5>Aucune vente enregistrée</h5>
                                    <p class="text-muted">Commencez par faire une vente</p>
                                    <a href="{{ route('client.sales.pos') }}" class="btn btn-success">
                                        <i class="fas fa-cash-register me-1"></i> Nouvelle vente
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Affichage de {{ $orders->firstItem() ?? 0 }} à {{ $orders->lastItem() ?? 0 }} 
                    sur {{ $orders->total() }} ventes
                </small>
                {{ $orders->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    .border-left-primary {
        border-left: 4px solid #4A6CF7 !important;
    }
    .border-left-success {
        border-left: 4px solid #22C55E !important;
    }
    .border-left-info {
        border-left: 4px solid #06B6D4 !important;
    }
    .border-left-warning {
        border-left: 4px solid #F59E0B !important;
    }
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
</style>
@endpush