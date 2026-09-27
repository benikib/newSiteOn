@extends('layouts.main')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- ===== EN-TÊTE ===== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-exchange-alt me-2 text-info"></i> Mouvements de stock
            </h4>
            <p class="text-muted mb-0 small">
                Historique complet des entrées et sorties de stock
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
                <form action="{{ route('client.stocks.movements') }}" method="GET" class="row g-3">
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

    <!-- ===== STATISTIQUES RAPIDES ===== -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-left-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total entrées</span>
                            <h3 class="mb-0 text-success">{{ $totalIn ?? 0 }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-arrow-down text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-left-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total sorties</span>
                            <h3 class="mb-0 text-danger">{{ $totalOut ?? 0 }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="fas fa-arrow-up text-danger fs-4"></i>
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
                            <span class="text-muted small">Ajustements</span>
                            <h3 class="mb-0 text-info">{{ $totalAdjust ?? 0 }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-edit text-info fs-4"></i>
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
                            <span class="text-muted small">Total mouvements</span>
                            <h3 class="mb-0 text-warning">{{ $movements->total() ?? 0 }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-exchange-alt text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== FILTRES ===== -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('client.stocks.movements') }}" method="GET" class="row g-3">
                @if(isset($selectedEtablissementId))
                    <input type="hidden" name="etablissement_id" value="{{ $selectedEtablissementId }}">
                @endif
                
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">Tous les types</option>
                        <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Entrée</option>
                        <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Sortie</option>
                        <option value="adjust_positive" {{ request('type') == 'adjust_positive' ? 'selected' : '' }}>Ajustement +</option>
                        <option value="adjust_negative" {{ request('type') == 'adjust_negative' ? 'selected' : '' }}>Ajustement -</option>
                        <option value="inventory_in" {{ request('type') == 'inventory_in' ? 'selected' : '' }}>Inventaire +</option>
                        <option value="inventory_out" {{ request('type') == 'inventory_out' ? 'selected' : '' }}>Inventaire -</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="product_id" class="form-select">
                        <option value="">Tous les produits</option>
                        @foreach($products ?? [] as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" 
                           placeholder="Date début" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" 
                           placeholder="Date fin" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ===== LISTE DES MOUVEMENTS ===== -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Produit</th>
                            <th>Code</th>
                            <th>Quantité</th>
                            <th>Avant</th>
                            <th>Après</th>
                            <th>Variation</th>
                            <th>Note</th>
                            <th>Utilisateur</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                            <tr>
                                <td>
                                    <small>{{ $movement->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    @php
                                        $typeColors = [
                                            'in' => 'success',
                                            'out' => 'danger',
                                            'adjust_positive' => 'info',
                                            'adjust_negative' => 'warning',
                                            'inventory_in' => 'primary',
                                            'inventory_out' => 'secondary',
                                        ];
                                        $typeLabels = [
                                            'in' => 'Entrée',
                                            'out' => 'Sortie',
                                            'adjust_positive' => 'Ajustement +',
                                            'adjust_negative' => 'Ajustement -',
                                            'inventory_in' => 'Inventaire +',
                                            'inventory_out' => 'Inventaire -',
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $typeColors[$movement->type] ?? 'secondary' }}">
                                        {{ $typeLabels[$movement->type] ?? $movement->type }}
                                    </span>
                                </td>
                                <td>
                                    <strong>{{ $movement->product->name ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $movement->product->code ?? 'N/A' }}</span>
                                </td>
                                <td class="fw-bold">
                                    {{ number_format($movement->quantity) }}
                                </td>
                                <td>{{ number_format($movement->before) }}</td>
                                <td>{{ number_format($movement->after) }}</td>
                                <td>
                                    @php
                                        $diff = $movement->after - $movement->before;
                                        $color = $diff > 0 ? 'text-success' : ($diff < 0 ? 'text-danger' : 'text-muted');
                                    @endphp
                                    <span class="fw-bold {{ $color }}">
                                        {{ $diff > 0 ? '+' : '' }}{{ number_format($diff) }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ Str::limit($movement->note, 30) }}</small>
                                </td>
                                <td>
                                    <small>{{ $movement->user->name ?? 'N/A' }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="fas fa-exchange-alt fa-3x text-muted mb-3 d-block"></i>
                                    <h5>Aucun mouvement</h5>
                                    <p class="text-muted">Commencez par effectuer des entrées ou sorties de stock</p>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('client.stocks.stock-in') }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-plus"></i> Entrée de stock
                                        </a>
                                        <a href="{{ route('client.stocks.stock-out') }}" class="btn btn-danger btn-sm">
                                            <i class="fas fa-minus"></i> Sortie de stock
                                        </a>
                                    </div>
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
                    Affichage de {{ $movements->firstItem() ?? 0 }} à {{ $movements->lastItem() ?? 0 }} 
                    sur {{ $movements->total() }} mouvements
                </small>
                {{ $movements->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    .border-left-success {
        border-left: 4px solid #22C55E !important;
    }
    .border-left-danger {
        border-left: 4px solid #EF4444 !important;
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