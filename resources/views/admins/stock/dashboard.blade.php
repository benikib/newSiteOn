@extends('layouts.stockadmin')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- ===== EN-TÊTE ===== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-chart-pie me-2 text-primary"></i> Tableau de bord des stocks
            </h4>
            <p class="text-muted mb-0 small">
                Vue d'ensemble de votre gestion de stock
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('admin.stocks.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
        </div>
    </div>

    <!-- ===== STATISTIQUES ===== -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total produits</span>
                            <h3 class="mb-0">{{ number_format($stats['total_products']) }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-boxes text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Valeur stock</span>
                            <h5 class="mb-0 text-success">{{ number_format($stats['total_value'], 2, ',', ' ') }} Fc</h5>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-euro-sign text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Stock bas</span>
                            <h3 class="mb-0 text-warning">{{ $stats['low_stock_count'] }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-exclamation-triangle text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">En rupture</span>
                            <h3 class="mb-0 text-danger">{{ $stats['out_of_stock_count'] }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="fas fa-times-circle text-danger fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== ALERTES ===== -->
    @if($stats['out_of_stock_count'] > 0 || $stats['low_stock_count'] > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Attention :</strong>
            @if($stats['out_of_stock_count'] > 0)
                {{ $stats['out_of_stock_count'] }} produit(s) en rupture
            @endif
            @if($stats['out_of_stock_count'] > 0 && $stats['low_stock_count'] > 0)
                et
            @endif
            @if($stats['low_stock_count'] > 0)
                {{ $stats['low_stock_count'] }} produit(s) en stock bas
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- ===== TOP PRODUITS & ALERTES ===== -->
    <div class="row g-3">

        <!-- Top produits -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-star text-warning me-2"></i> Top produits en stock</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produit</th>
                                    <th>Établissement</th>
                                    <th class="text-end">Quantité</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $item)
                                    <tr>
                                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                                        <td>{{ $item->etablissement->nom ?? 'N/A' }}</td>
                                        <td class="text-end fw-bold">{{ number_format($item->quantity) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">Aucun produit</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produits en rupture -->
        <div class="col-md-6">
            <div class="card shadow-sm border-danger">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-times-circle text-danger me-2"></i> Produits en rupture</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produit</th>
                                    <th>Établissement</th>
                                    <th class="text-end">Quantité</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($outOfStock as $item)
                                    <tr>
                                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                                        <td>{{ $item->etablissement->nom ?? 'N/A' }}</td>
                                        <td class="text-end text-danger fw-bold">{{ number_format($item->quantity) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-success">
                                            <i class="fas fa-check-circle me-1"></i> Aucun produit en rupture
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock bas -->
        <div class="col-md-6">
            <div class="card shadow-sm border-warning">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle text-warning me-2"></i> Stock bas</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produit</th>
                                    <th>Établissement</th>
                                    <th class="text-end">Quantité</th>
                                    <th class="text-end">Min</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStock as $item)
                                    <tr>
                                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                                        <td>{{ $item->etablissement->nom ?? 'N/A' }}</td>
                                        <td class="text-end text-warning fw-bold">{{ number_format($item->quantity) }}</td>
                                        <td class="text-end">{{ number_format($item->minimum_stock) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-success">
                                            <i class="fas fa-check-circle me-1"></i> Aucun stock bas
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Valeur par établissement -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-building me-2 text-primary"></i> Valeur par établissement</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Établissement</th>
                                    <th class="text-end">Produits</th>
                                    <th class="text-end">Valeur</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($valueByEtablissement as $item)
                                    <tr>
                                        <td>{{ $item['name'] }}</td>
                                        <td class="text-end">{{ number_format($item['total_products']) }}</td>
                                        <td class="text-end text-success">
                                            {{ number_format($item['total_value'], 2, ',', ' ') }} Fc
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">Aucun établissement</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection