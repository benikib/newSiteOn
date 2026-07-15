@extends('layouts.main')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-warehouse me-2 text-primary"></i> Gestion des stocks
            </h4>
            <p class="text-muted mb-0 small">
                Gérez les stocks de vos établissements
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('client.stocks.stock-in') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus-circle me-1"></i> Entrée
            </a>
            <a href="{{ route('client.stocks.stock-out') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-minus-circle me-1"></i> Sortie
            </a>
        </div>
    </div>

    <!-- Sélecteur d'établissement -->
    @if(isset($etablissements) && $etablissements->count() > 1)
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('client.stocks.index') }}" method="GET" class="row g-3">
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
                    @if(request()->has('search') || request()->has('category_id') || request()->has('stock_status'))
                        <div class="col-md-8 d-flex align-items-end">
                            <a href="{{ route('client.stocks.index', ['etablissement_id' => $selectedEtablissementId ?? '']) }}" 
                               class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i> Réinitialiser les filtres
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    @endif

    <!-- Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('client.stocks.index') }}" method="GET" class="row g-3">
                @if(isset($selectedEtablissementId))
                    <input type="hidden" name="etablissement_id" value="{{ $selectedEtablissementId }}">
                @endif
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" 
                               placeholder="Rechercher..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories ?? [] as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="stock_status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="normal" {{ request('stock_status') == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stock bas</option>
                        <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Rupture</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des stocks -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Produit</th>
                            <th>Code</th>
                            <th>Catégorie</th>
                            <th>Quantité</th>
                            <th>Min</th>
                            <th>Prix achat</th>
                            <th>Prix vente</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stocks ?? [] as $stock)
                            <tr>
                                <td>{{ $stock->id }}</td>
                                <td>
                                    <strong>{{ $stock->product->name ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $stock->product->unit->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $stock->product->code ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $stock->product->category->nom ?? 'N/A' }}</td>
                                <td>
                                    <span class="fw-bold {{ ($stock->quantity ?? 0) <= ($stock->minimum_stock ?? 0) ? 'text-danger' : '' }}">
                                        {{ number_format($stock->quantity ?? 0) }}
                                    </span>
                                </td>
                                <td>{{ number_format($stock->minimum_stock ?? 0) }}</td>
                                <td>{{ number_format($stock->purchase_price ?? 0, 2, ',', ' ') }} Fc</td>
                                <td>{{ number_format($stock->selling_price ?? 0, 2, ',', ' ') }} Fc</td>
                                <td>
                                    @if(($stock->quantity ?? 0) <= 0)
                                        <span class="badge bg-danger">Rupture</span>
                                    @elseif(($stock->quantity ?? 0) <= ($stock->minimum_stock ?? 0))
                                        <span class="badge bg-warning text-dark">Stock bas</span>
                                    @else
                                        <span class="badge bg-success">Normal</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('client.stocks.stock-in') }}" 
                                           class="btn btn-sm btn-success" title="Ajouter">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                        <a href="{{ route('client.stocks.stock-out') }}" 
                                           class="btn btn-sm btn-danger" title="Retirer">
                                            <i class="fas fa-minus"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="fas fa-warehouse fa-3x text-muted mb-3 d-block"></i>
                                    <h5>Aucun produit en stock</h5>
                                    <p class="text-muted">Commencez par faire une entrée de stock</p>
                                    <a href="{{ route('client.stocks.stock-in') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Ajouter un stock
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(isset($stocks) && $stocks->hasPages())
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Affichage de {{ $stocks->firstItem() ?? 0 }} à {{ $stocks->lastItem() ?? 0 }} 
                        sur {{ $stocks->total() }}
                    </small>
                    {{ $stocks->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection