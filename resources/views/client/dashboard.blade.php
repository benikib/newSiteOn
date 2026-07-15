@extends('layouts.main')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- ===== EN-TÊTE AVEC SÉLECTEUR D'ÉTABLISSEMENT ===== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-chart-pie me-2 text-primary"></i> Tableau de bord
            </h4>
            <p class="text-muted mb-0 small">
                {{ $etablissement->name ?? 'Sélectionnez un établissement' }} - Gestion de stock
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            @if(isset($etablissements) && $etablissements->count() > 1)
                <form action="{{ route('client.dashboard') }}" method="GET" class="d-flex gap-2">
                    <select name="etablissement_id" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
                        @foreach($etablissements as $etab)
                            <option value="{{ $etab->id }}" {{ ($selectedEtablissementId ?? $etab->id) == $etab->id ? 'selected' : '' }}>
                                {{ $etab->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
            <a href="{{ route('client.stocks.stock-in') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus-circle me-1"></i> Entrée
            </a>
            <a href="{{ route('client.stocks.stock-out') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-minus-circle me-1"></i> Sortie
            </a>
        </div>
    </div>

    <!-- ===== STATISTIQUES GÉNÉRALES ===== -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-left-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Total produits</span>
                            <h3 class="mb-0">{{ number_format($stats['total_products'] ?? 0) }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-boxes text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-left-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Valeur du stock</span>
                            <h5 class="mb-0 text-success">{{ number_format($stats['total_value'] ?? 0, 2, ',', ' ') }} Fc</h5>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-euro-sign text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-left-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Stock bas</span>
                            <h3 class="mb-0 text-warning">{{ $stats['low_stock_count'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-exclamation-triangle text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm border-left-danger h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">En rupture</span>
                            <h3 class="mb-0 text-danger">{{ $stats['out_of_stock_count'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="fas fa-times-circle text-danger fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== ALERTES DE STOCK ===== -->
    @if(isset($alerts) && $alerts->count() > 0)
        <div class="card shadow-sm mb-4 border-warning">
            <div class="card-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-bell text-warning me-2"></i>
                    Alertes de stock ({{ $alerts->count() }})
                </h6>
                <a href="{{ route('client.stocks.alerts') }}" class="btn btn-sm btn-outline-warning">
                    <i class="fas fa-eye me-1"></i> Voir tout
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produit</th>
                                <th>Quantité</th>
                                <th>Minimum</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alerts as $alert)
                                <tr>
                                    <td>
                                        <strong>{{ $alert['product'] }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $alert['category'] ?? 'N/A' }}</small>
                                    </td>
                                    <td class="fw-bold {{ $alert['type'] == 'out_of_stock' ? 'text-danger' : 'text-warning' }}">
                                        {{ number_format($alert['quantity']) }}
                                    </td>
                                    <td>{{ number_format($alert['minimum']) }}</td>
                                    <td>
                                        @if($alert['type'] == 'out_of_stock')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i> Rupture
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-exclamation-triangle me-1"></i> Stock bas
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('client.stocks.stock-in') }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-plus"></i> Approvisionner
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- ===== STOCK PAR PRODUIT & MOUVEMENTS ===== -->
    <div class="row g-3">
        <!-- Stock par produit -->
        <div class="col-xl-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-warehouse me-2 text-primary"></i>
                        Stock par produit
                        <span class="badge bg-secondary ms-2">{{ $stockByEtablissement->count() ?? 0 }}</span>
                    </h6>
                    <a href="{{ route('client.stocks.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-arrow-right me-1"></i> Voir tout
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produit</th>
                                    <th>Code</th>
                                    <th>Quantité</th>
                                    <th>Valeur</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($stockByEtablissement ?? collect())->take(10) as $stock)
                                    <tr>
                                        <td>
                                            <strong>{{ $stock->product->name ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $stock->product->unit->name ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $stock->product->code ?? 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold {{ ($stock->quantity ?? 0) <= ($stock->minimum_stock ?? 0) ? 'text-danger' : '' }}">
                                                {{ number_format($stock->quantity ?? 0) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ number_format(($stock->quantity ?? 0) * ($stock->purchase_price ?? 0), 2, ',', ' ') }} Fc
                                        </td>
                                        <td>
                                            @if(($stock->quantity ?? 0) <= 0)
                                                <span class="badge bg-danger">Rupture</span>
                                            @elseif(($stock->quantity ?? 0) <= ($stock->minimum_stock ?? 0))
                                                <span class="badge bg-warning text-dark">Stock bas</span>
                                            @else
                                                <span class="badge bg-success">Normal</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="fas fa-box-open fa-3x text-muted mb-3 d-block"></i>
                                            <p class="text-muted">Aucun produit en stock</p>
                                            <a href="{{ route('client.stocks.stock-in') }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-plus"></i> Ajouter un stock
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Derniers mouvements -->
        <div class="col-xl-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-history me-2 text-info"></i>
                        Derniers mouvements
                    </h6>
                    <a href="{{ route('client.stocks.movements') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-arrow-right me-1"></i> Voir tout
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse(($recentMovements ?? collect())->take(8) as $movement)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge {{ $movement->type_badge ?? 'bg-secondary' }}">
                                            {{ $movement->type_label ?? $movement->type ?? 'N/A' }}
                                        </span>
                                        <span class="fw-semibold">{{ $movement->product->name ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="fw-bold">{{ number_format($movement->quantity ?? 0) }}</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <small class="text-muted">{{ $movement->created_at->diffForHumans() ?? 'N/A' }}</small>
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>
                                        {{ $movement->user->name ?? 'N/A' }}
                                    </small>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted py-4">
                                <i class="fas fa-clock fa-2x mb-2 d-block"></i>
                                Aucun mouvement
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== VALORISATION DU STOCK ===== -->
    <div class="row g-3 mt-2">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2 text-success"></i>
                        Valorisation du stock
                    </h6>
                    <a href="{{ route('client.stocks.valuation') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-arrow-right me-1"></i> Voir le détail
                    </a>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <label class="text-muted small text-uppercase fw-semibold">Valeur d'achat</label>
                                <h5 class="text-success">
                                    {{ number_format($stockValuation->total_purchase_value ?? 0, 2, ',', ' ') }} Fc
                                </h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <label class="text-muted small text-uppercase fw-semibold">Valeur de vente</label>
                                <h5 class="text-primary">
                                    {{ number_format($stockValuation->total_selling_value ?? 0, 2, ',', ' ') }} Fc
                                </h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <label class="text-muted small text-uppercase fw-semibold">Profit potentiel</label>
                                <h5 class="text-info">
                                    {{ number_format($stockValuation->total_profit ?? 0, 2, ',', ' ') }} Fc
                                </h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded-3">
                                <label class="text-muted small text-uppercase fw-semibold">Quantité totale</label>
                                <h5>{{ number_format($stockValuation->total_quantity ?? 0) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
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
    .border-left-warning {
        border-left: 4px solid #F59E0B !important;
    }
    .border-left-danger {
        border-left: 4px solid #EF4444 !important;
    }
    .border-left-info {
        border-left: 4px solid #06B6D4 !important;
    }
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    .list-group-item {
        border-left: none;
        border-right: none;
    }
    .list-group-item:first-child {
        border-top: none;
    }
</style>
@endpush