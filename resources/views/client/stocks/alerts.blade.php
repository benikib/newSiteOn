@extends('layouts.main')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- ===== EN-TÊTE ===== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-bell me-2 text-warning"></i> Alertes de stock
            </h4>
            <p class="text-muted mb-0 small">
                Produits en stock bas ou en rupture
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('client.stocks.stock-in') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus-circle me-1"></i> Entrée de stock
            </a>
            <a href="{{ route('client.stocks.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <!-- ===== SÉLECTEUR D'ÉTABLISSEMENT ===== -->
    @if(isset($etablissements) && $etablissements->count() > 1)
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('client.stocks.alerts') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
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

    <!-- ===== STATISTIQUES ===== -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-left-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">En rupture</span>
                            <h3 class="mb-0 text-danger">{{ $outOfStock->total() ?? 0 }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="fas fa-times-circle text-danger fs-4"></i>
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
                            <span class="text-muted small">Stock bas</span>
                            <h3 class="mb-0 text-warning">{{ $lowStock->total() ?? 0 }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-exclamation-triangle text-warning fs-4"></i>
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
                            <span class="text-muted small">Stock normal</span>
                            <h3 class="mb-0 text-success">{{ $normalStock ?? 0 }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-check-circle text-success fs-4"></i>
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
                            <span class="text-muted small">Total produits</span>
                            <h3 class="mb-0 text-info">{{ $totalProducts ?? 0 }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-boxes text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== PRODUITS EN RUPTURE ===== -->
    <div class="card shadow-sm mb-4 border-danger">
        <div class="card-header bg-danger bg-opacity-10 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-times-circle text-danger me-2"></i>
                Produits en rupture
                <span class="badge bg-danger ms-2">{{ $outOfStock->total() ?? 0 }}</span>
            </h6>
        </div>
        <div class="card-body p-0">
            @if($outOfStock->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produit</th>
                                <th>Code</th>
                                <th>Catégorie</th>
                                <th>Quantité</th>
                                <th>Stock minimum</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($outOfStock as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $item->product->unit->name ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $item->product->code ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $item->product->category->nom ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-danger fs-6">
                                            {{ number_format($item->quantity) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($item->minimum_stock) }}</td>
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
                <div class="card-footer bg-white">
                    {{ $outOfStock->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-3 d-block"></i>
                    <h5>Aucun produit en rupture</h5>
                    <p class="text-muted">Tous vos produits sont disponibles</p>
                </div>
            @endif
        </div>
    </div>

    <!-- ===== PRODUITS EN STOCK BAS ===== -->
    <div class="card shadow-sm border-warning">
        <div class="card-header bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                Produits en stock bas
                <span class="badge bg-warning ms-2">{{ $lowStock->total() ?? 0 }}</span>
            </h6>
        </div>
        <div class="card-body p-0">
            @if($lowStock->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produit</th>
                                <th>Code</th>
                                <th>Catégorie</th>
                                <th>Quantité</th>
                                <th>Stock minimum</th>
                                <th>Pourcentage</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStock as $item)
                                @php
                                    $percentage = $item->minimum_stock > 0 ? round(($item->quantity / $item->minimum_stock) * 100) : 0;
                                    $barColor = $percentage <= 25 ? 'bg-danger' : ($percentage <= 50 ? 'bg-warning' : 'bg-info');
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $item->product->unit->name ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $item->product->code ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $item->product->category->nom ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-warning text-dark fs-6">
                                            {{ number_format($item->quantity) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($item->minimum_stock) }}</td>
                                    <td style="min-width: 120px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 8px;">
                                                <div class="progress-bar {{ $barColor }}" 
                                                     role="progressbar" 
                                                     style="width: {{ $percentage }}%;" 
                                                     aria-valuenow="{{ $percentage }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                            <span class="small fw-bold">{{ $percentage }}%</span>
                                        </div>
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
                <div class="card-footer bg-white">
                    {{ $lowStock->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-3 d-block"></i>
                    <h5>Aucun produit en stock bas</h5>
                    <p class="text-muted">Tous vos produits ont un stock suffisant</p>
                </div>
            @endif
        </div>
    </div>

</div>



<style>
    .border-left-danger {
        border-left: 4px solid #EF4444 !important;
    }
    .border-left-warning {
        border-left: 4px solid #F59E0B !important;
    }
    .border-left-success {
        border-left: 4px solid #22C55E !important;
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
    .progress {
        border-radius: 10px;
        background-color: #e9ecef;
    }
    .progress-bar {
        border-radius: 10px;
        transition: width 0.6s ease;
    }
    .badge.fs-6 {
        font-size: 1rem !important;
        padding: 0.4rem 0.8rem;
    }
</style>
@endsection