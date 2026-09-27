@extends('layouts.main')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-tags me-2 text-primary"></i> Catégories
            </h4>
            <p class="text-muted mb-0 small">
                Gérez vos catégories de produits
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('client.categories.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Nouvelle catégorie
            </a>
        </div>
    </div>

    <!-- Sélecteur d'établissement -->
    @if(isset($etablissements) && $etablissements->count() > 1)
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form action="{{ route('client.categories.index') }}" method="GET" class="row g-3">
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
            <form action="{{ route('client.categories.index') }}" method="GET" class="row g-3">
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
                    <select name="statut" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ request('statut') == 'inactif' ? 'selected' : '' }}>Inactif</option>
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

    <!-- Liste -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Produits</th>
                            <th>Statut</th>
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
                                <td>{{ Str::limit($category->description, 50) }}</td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $category->products_count ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $category->statut ? 'bg-success' : 'bg-danger' }}">
                                        {{ $category->statut ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('client.categories.edit', $category->id) }}" 
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('client.categories.toggle-status', $category->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{ $category->statut ? 'btn-outline-success' : 'btn-outline-danger' }}" 
                                                    title="{{ $category->statut ? 'Désactiver' : 'Activer' }}">
                                                <i class="fas {{ $category->statut ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                            </button>
                                        </form>
                                        @if(($category->products_count ?? 0) == 0)
                                            <form action="{{ route('client.categories.destroy', $category->id) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Supprimer cette catégorie ?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary" disabled title="Contient des produits">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-tags fa-3x text-muted mb-3 d-block"></i>
                                    <h5>Aucune catégorie</h5>
                                    <a href="{{ route('client.categories.create') }}" class="btn btn-primary">
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
                    sur {{ $categories->total() }}
                </small>
                {{ $categories->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

</div>
@endsection