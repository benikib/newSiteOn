@extends('layouts.main')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- ===== EN-TÊTE ===== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-exclamation-triangle me-2 text-danger"></i> Produits en rupture
            </h4>
            <p class="text-muted mb-0 small">
                Liste des produits dont le stock est épuisé
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
                <form action="{{ route('client.stocks.out-of-stock') }}" method="GET" class="row g-3">
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
                            <h3 class="mb-0 text-danger">{{ $stocks->total() ?? 0 }}</h3>
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
                            <h3 class="mb-0 text-warning">{{ $lowStockCount ?? 0 }}</h3>
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
                            <h3 class="mb-0 text-success">{{ $normalStockCount ?? 0 }}</h3>
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

    <!-- ===== LISTE DES PRODUITS EN RUPTURE ===== -->
    <div class="card shadow-sm border-danger">
        <div class="card-header bg-danger bg-opacity-10 d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-times-circle text-danger me-2"></i>
                Produits en rupture
                <span class="badge bg-danger ms-2">{{ $stocks->total() ?? 0 }}</span>
            </h6>
            <div>
                <a href="{{ route('client.stocks.stock-in') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus"></i> Approvisionner
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($stocks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th style="width: 60px;">Image</th>
                                <th>Produit</th>
                                <th>Code</th>
                                <th>Catégorie</th>
                                <th>Unité</th>
                                <th>Quantité</th>
                                <th>Stock min</th>
                                <th>Dernière mise à jour</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stocks as $stock)
                                <tr>
                                    <td>{{ $stock->id }}</td>
                                    <td>
                                        <img src="{{ $stock->product->image_url ?? asset('assets/img/default-product.png') }}" 
                                             alt="{{ $stock->product->name }}" 
                                             class="rounded"
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <strong>{{ $stock->product->name ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $stock->product->description ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $stock->product->code ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $stock->product->category->nom ?? 'N/A' }}</td>
                                    <td>{{ $stock->product->unit->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-danger fs-6">
                                            <i class="fas fa-times-circle me-1"></i>
                                            {{ number_format($stock->quantity) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($stock->minimum_stock) }}</td>
                                    <td>
                                        <small>{{ $stock->updated_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('client.stocks.stock-in') }}" 
                                               class="btn btn-sm btn-success" title="Approvisionner">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                            <a href="{{ route('client.stocks.stock-out') }}" 
                                               class="btn btn-sm btn-danger" title="Sortie">
                                                <i class="fas fa-minus"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Affichage de {{ $stocks->firstItem() ?? 0 }} à {{ $stocks->lastItem() ?? 0 }} 
                            sur {{ $stocks->total() }} produits en rupture
                        </small>
                        {{ $stocks->appends(request()->query())->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-4x text-success mb-3 d-block"></i>
                    <h5 class="text-success">🎉 Aucun produit en rupture !</h5>
                    <p class="text-muted">Tous vos produits sont disponibles en stock</p>
                    <div class="d-flex gap-2 justify-content-center mt-3">
                        <a href="{{ route('client.stocks.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i> Voir tous les stocks
                        </a>
                        <a href="{{ route('client.stocks.stock-in') }}" class="btn btn-success">
                            <i class="fas fa-plus me-1"></i> Ajouter du stock
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('styles')
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
    .badge.fs-6 {
        font-size: 1rem !important;
        padding: 0.4rem 0.8rem;
    }
</style>
@endpush