@extends('layouts.main')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-boxes me-2 text-primary"></i> Mes produits
            </h4>
            <p class="text-muted mb-0 small">
                Gérez votre catalogue de produits
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('client.products.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Ajouter un produit
            </a>
        </div>
    </div>

    <!-- Sélecteur d'établissement -->
    @if(isset($etablissements) && $etablissements->count() > 1)
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('client.products.index') }}" method="GET" class="row g-3">
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

    <!-- Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('client.products.index') }}" method="GET" class="row g-3">
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
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
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

    <!-- Liste des produits -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px">#</th>
                            <th style="width: 60px">Image</th>
                            <th>Nom</th>
                            <th>Code</th>
                            <th>Catégorie</th>
                            <th>Unité</th>
                            <th>Stock</th>
                            <th style="width: 100px">Statut</th>
                            <th style="width: 150px" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products ?? [] as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>
                                    <img src="{{ $product->image_url ?? asset('assets/img/default-product.png') }}" 
                                         alt="{{ $product->name }}" 
                                         class="rounded" 
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                </td>
                                <td>
                                    <strong>{{ $product->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($product->description, 30) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $product->code }}</span>
                                    @if($product->barcode)
                                        <br>
                                        <small class="text-muted">{{ $product->barcode }}</small>
                                    @endif
                                </td>
                                <td>{{ $product->category->nom ?? 'N/A' }}</td>
                                <td>{{ $product->unit->name ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $stockQty = $product->stock->quantity ?? 0;
                                        $minStock = $product->stock->minimum_stock ?? 0;
                                    @endphp
                                    <span class="badge {{ $stockQty <= 0 ? 'bg-danger' : ($stockQty <= $minStock ? 'bg-warning text-dark' : 'bg-primary') }}">
                                        {{ number_format($stockQty) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $product->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $product->status ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('client.products.edit', $product->id) }}" 
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('client.products.toggle-status', $product->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $product->status ? 'btn-outline-success' : 'btn-outline-danger' }}" 
                                                    title="{{ $product->status ? 'Désactiver' : 'Activer' }}">
                                                <i class="fas {{ $product->status ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('client.products.destroy', $product->id) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Supprimer ce produit ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-boxes fa-3x text-muted mb-3 d-block"></i>
                                    <h5>Aucun produit</h5>
                                    <p class="text-muted">Commencez par ajouter votre premier produit</p>
                                    <a href="{{ route('client.products.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Ajouter un produit
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(isset($products) && $products->hasPages())
            <div class="card-footer bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Affichage de {{ $products->firstItem() ?? 0 }} à {{ $products->lastItem() ?? 0 }} 
                        sur {{ $products->total() }}
                    </small>
                    {{ $products->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection