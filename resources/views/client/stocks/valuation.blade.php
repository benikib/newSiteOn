@extends('layouts.main')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- ===== EN-TÊTE ===== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-chart-bar me-2 text-success"></i> Valorisation du stock
            </h4>
            <p class="text-muted mb-0 small">
                Analyse de la valeur de votre stock
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('client.stocks.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <!-- ===== SÉLECTEUR D'ÉTABLISSEMENT ===== -->
    @if(isset($etablissements) && $etablissements->count() > 1)
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('client.stocks.valuation') }}" method="GET" class="row g-3">
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

    <!-- ===== RÉSUMÉ GLOBAL ===== -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-left-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total articles</span>
                            <h3 class="mb-0">{{ number_format($summary->total_items ?? 0) }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-boxes text-primary fs-4"></i>
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
                            <span class="text-muted small">Quantité totale</span>
                            <h3 class="mb-0">{{ number_format($summary->total_quantity ?? 0) }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-cubes text-success fs-4"></i>
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
                            <span class="text-muted small">Valeur d'achat</span>
                            <h5 class="mb-0 text-info">
                                {{ number_format($summary->total_purchase_value ?? 0, 2, ',', ' ') }} Fc
                            </h5>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-euro-sign text-info fs-4"></i>
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
                            <span class="text-muted small">Profit potentiel</span>
                            <h5 class="mb-0 text-warning">
                                {{ number_format($summary->total_profit ?? 0, 2, ',', ' ') }} Fc
                            </h5>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-chart-line text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== VALORISATION PAR CATÉGORIE ===== -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h6 class="mb-0">
                <i class="fas fa-tags me-2 text-primary"></i>
                Valorisation par catégorie
            </h6>
        </div>
        <div class="card-body p-0">
            @if($byCategory->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Catégorie</th>
                                <th>Quantité</th>
                                <th>Valeur d'achat</th>
                                <th>Valeur de vente</th>
                                <th>Profit potentiel</th>
                                <th>% du stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalValue = $byCategory->sum('purchase_value');
                            @endphp
                            @foreach($byCategory as $category)
                                @php
                                    $percentage = $totalValue > 0 ? round(($category->purchase_value / $totalValue) * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $category->nom }}</strong>
                                    </td>
                                    <td>{{ number_format($category->quantity) }}</td>
                                    <td class="text-success">
                                        {{ number_format($category->purchase_value, 2, ',', ' ') }} Fc
                                    </td>
                                    <td class="text-primary">
                                        {{ number_format($category->selling_value, 2, ',', ' ') }} Fc
                                    </td>
                                    <td class="text-warning">
                                        {{ number_format($category->profit, 2, ',', ' ') }} Fc
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 8px;">
                                                <div class="progress-bar bg-primary" 
                                                     role="progressbar" 
                                                     style="width: {{ $percentage }}%;" 
                                                     aria-valuenow="{{ $percentage }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                            <span class="small">{{ $percentage }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td>TOTAL</td>
                                <td>{{ number_format($byCategory->sum('quantity')) }}</td>
                                <td class="text-success">{{ number_format($byCategory->sum('purchase_value'), 2, ',', ' ') }} Fc</td>
                                <td class="text-primary">{{ number_format($byCategory->sum('selling_value'), 2, ',', ' ') }} Fc</td>
                                <td class="text-warning">{{ number_format($byCategory->sum('profit'), 2, ',', ' ') }} Fc</td>
                                <td>100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-chart-bar fa-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted">Aucune donnée de valorisation disponible</p>
                </div>
            @endif
        </div>
    </div>

    <!-- ===== TOP PRODUITS ===== -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h6 class="mb-0">
                <i class="fas fa-crown me-2 text-warning"></i>
                Top 10 des produits les plus valorisés
            </h6>
        </div>
        <div class="card-body p-0">
            @if($topProducts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Produit</th>
                                <th>Code</th>
                                <th>Quantité</th>
                                <th>Prix achat</th>
                                <th>Prix vente</th>
                                <th>Valeur totale</th>
                                <th>Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topProducts as $index => $product)
                                @php
                                    $totalValue = $product->quantity * $product->purchase_price;
                                    $profit = $product->quantity * ($product->selling_price - $product->purchase_price);
                                @endphp
                                <tr>
                                    <td>
                                        <span class="badge {{ $index < 3 ? 'bg-warning text-dark' : 'bg-secondary' }}">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $product->product->name ?? 'N/A' }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $product->product->code ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ number_format($product->quantity) }}</td>
                                    <td>{{ number_format($product->purchase_price, 2, ',', ' ') }} Fc</td>
                                    <td>{{ number_format($product->selling_price, 2, ',', ' ') }} Fc</td>
                                    <td class="text-success">
                                        {{ number_format($totalValue, 2, ',', ' ') }} Fc
                                    </td>
                                    <td class="text-warning">
                                        {{ number_format($profit, 2, ',', ' ') }} Fc
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-crown fa-3x text-muted mb-3 d-block"></i>
                    <p class="text-muted">Aucun produit en stock</p>
                </div>
            @endif
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
    .progress {
        border-radius: 10px;
        background-color: #e9ecef;
    }
    .progress-bar {
        border-radius: 10px;
        transition: width 0.6s ease;
    }
</style>
@endpush