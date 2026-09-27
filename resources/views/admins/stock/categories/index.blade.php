@extends('layouts.stockadmin')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold">
                <i class="fas fa-tags me-2 text-primary"></i> Gestion des Catégories
            </h4>
            <p class="text-muted small mb-0">Organisez vos produits par catégories</p>
        </div>
        <div>
            <a href="{{ route('stock.categories.export') }}" class="btn btn-sm btn-success me-2">
                <i class="fas fa-file-export"></i> Exporter
            </a>
            <a href="{{ route('stock.categories.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus"></i> Nouvelle catégorie
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total</span>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-tags text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Actives</span>
                            <h3 class="mb-0 text-success">{{ $stats['actif'] }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Inactives</span>
                            <h3 class="mb-0 text-danger">{{ $stats['inactif'] }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="fas fa-times-circle text-danger fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Total Produits</span>
                            <h3 class="mb-0 text-info">{{ $stats['total_products'] }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-box text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('stock.categories.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Rechercher une catégorie..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Tous les statuss</option>
                        <option value="actif" {{ request('status') == 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ request('status') == 'inactif' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="has_products" class="form-select">
                        <option value="">Toutes</option>
                        <option value="yes" {{ request('has_products') == 'yes' ? 'selected' : '' }}>Avec produits</option>
                        <option value="no" {{ request('has_products') == 'no' ? 'selected' : '' }}>Sans produits</option>
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

    <!-- Liste des catégories -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Liste des catégories</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>
                                <a href="{{ route('stock.categories.index', array_merge(request()->all(), ['sort' => request('sort') == 'nom_asc' ? 'nom_desc' : 'nom_asc'])) }}" 
                                   class="text-decoration-none text-dark">
                                    Nom
                                    @if(request('sort') == 'nom_asc') ↑ @elseif(request('sort') == 'nom_desc') ↓ @endif
                                </a>
                            </th>
                            <th>Description</th>
                            <th>status</th>
                            <th>
                                <a href="{{ route('stock.categories.index', array_merge(request()->all(), ['sort' => request('sort') == 'products_desc' ? 'products_asc' : 'products_desc'])) }}" 
                                   class="text-decoration-none text-dark">
                                    Produits
                                    @if(request('sort') == 'products_desc') ↓ @elseif(request('sort') == 'products_asc') ↑ @endif
                                </a>
                            </th>
                            <th>Créé le</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>
                                    <strong>{{ $category->nom }}</strong>
                                </td>
                                <td>
                                    {{ Str::limit($category->description, 50) }}
                                </td>
                                <td>
                                    <span class="badge {{ $category->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $category->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $category->products_count > 0 ? 'bg-primary' : 'bg-secondary' }}">
                                        {{ $category->products_count }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ $category->created_at->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        <!-- Voir les produits -->
                                        <a href="{{ route('stock.categories.show', $category) }}" 
                                           class="btn btn-sm btn-outline-info" title="Voir les produits">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <!-- Activer/Désactiver -->
                                        <form action="{{ route('stock.categories.toggle-status', $category) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $category->status ? 'btn-outline-success' : 'btn-outline-danger' }}" 
                                                    title="{{ $category->status ? 'Désactiver' : 'Activer' }}">
                                                <i class="fas {{ $category->status ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                            </button>
                                        </form>
                                        
                                        <!-- Modifier -->
                                        <a href="{{ route('stock.categories.edit', $category) }}" 
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <!-- Supprimer -->
                                        @if($category->products_count == 0)
                                            <form action="{{ route('stock.categories.destroy', $category) }}" 
                                                  method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Supprimer cette catégorie ?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary" 
                                                    title="Impossible de supprimer (contient des produits)"
                                                    disabled>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-tags fa-3x text-muted mb-3 d-block"></i>
                                    <h5>Aucune catégorie trouvée</h5>
                                    <p class="text-muted">Commencez par créer votre première catégorie</p>
                                    <a href="{{ route('stock.categories.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Créer une catégorie
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
                    Affichage de {{ $categories->firstItem() ?? 0 }} à {{ $categories->lastItem() ?? 0 }} 
                    sur {{ $categories->total() }} catégorie(s)
                </small>
                {{ $categories->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection