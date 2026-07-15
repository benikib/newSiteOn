@extends('layouts.stockadmin')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- ===== EN-TÊTE ===== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-warehouse me-2 text-primary"></i> Détail du stock
            </h4>
            <p class="text-muted mb-0 small">
                {{ $stock->product->name }} - {{ $stock->etablissement->name }}
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('admin.stocks.edit', $stock) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit me-1"></i> Modifier
            </a>
            <a href="{{ route('admin.stocks.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <!-- ===== INFORMATIONS ===== -->
    <div class="row g-3">

        <!-- Carte principale -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Établissement</label>
                            <p class="fw-bold">{{ $stock->etablissement->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Produit</label>
                            <p class="fw-bold">{{ $stock->product->name }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Code produit</label>
                            <p><span class="badge bg-secondary">{{ $stock->product->code }}</span></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Catégorie</label>
                            <p>{{ $stock->product->category->nom ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Unité</label>
                            <p>{{ $stock->product->unit->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">Quantité</label>
                            <h4 class="fw-bold {{ $stock->quantity <= $stock->minimum_stock ? 'text-danger' : '' }}">
                                {{ number_format($stock->quantity) }}
                            </h4>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">Stock minimum</label>
                            <p class="fw-bold">{{ number_format($stock->minimum_stock) }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">Prix d'achat</label>
                            <p class="fw-bold">{{ number_format($stock->purchase_price, 2, ',', ' ') }} Fc</p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">Prix de vente</label>
                            <p class="fw-bold">{{ number_format($stock->selling_price, 2, ',', ' ') }} Fc</p>
                        </div>
                        <div class="col-md-12">
                            <label class="text-muted small">status</label>
                            <p>
                                @php $status = $stock->status; @endphp
                                <span class="badge {{ $status['badge'] }} fs-6">
                                    <i class="fas {{ $status['icon'] }} me-2"></i>
                                    {{ $status['label'] }}
                                </span>
                                @if($stock->quantity > 0 && $stock->minimum_stock > 0)
                                    <span class="ms-2 text-muted">
                                        ({{ number_format($stock->stock_percentage) }}% du minimum)
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte valeurs -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fas fa-chart-pie me-2 text-primary"></i> Valeurs</h6>
                    <div class="mb-3">
                        <label class="text-muted small">Valeur stock (achat)</label>
                        <h5 class="text-success">{{ number_format($stock->total_value, 2, ',', ' ') }} Fc</h5>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Valeur stock (vente)</label>
                        <h5 class="text-primary">{{ number_format($stock->total_selling_value, 2, ',', ' ') }} Fc</h5>
                    </div>
                    <div>
                        <label class="text-muted small">Profit potentiel</label>
                        <h5 class="text-info">{{ number_format($stock->potential_profit, 2, ',', ' ') }} Fc</h5>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection