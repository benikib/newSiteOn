@extends('layouts.stockadmin')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- ===== EN-TÊTE ===== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-tag me-2 text-primary"></i> {{ $category->nom }}
            </h4>
            <p class="text-muted mb-0 small">
                Détails de la catégorie et liste des produits associés
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('stock.categories.edit', $category) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit me-1"></i> Modifier
            </a>
            <a href="{{ route('stock.categories.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <!-- ===== INFORMATIONS ===== -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">status</span>
                            <div class="mt-1">
                                <span class="badge {{ $category->status ? 'bg-success' : 'bg-danger' }} fs-6">
                                    {{ $category->status ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        </div>
                        <div class="bg-{{ $category->status ? 'success' : 'danger' }} bg-opacity-10 p-3 rounded">
                            <i class="fas {{ $category->status ? 'fa-check-circle' : 'fa-times-circle' }} text-{{ $category->status ? 'success' : 'danger' }} fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Nombre de produits</span>
                            <h3 class="mb-0">{{ $category->products()->count() }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-box text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small">Créé le</span>
                            <div class="mt-1">
                                <small>{{ $category->created_at->format('d/m/Y à H:i') }}</small>
                            </div>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-calendar-alt text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== DESCRIPTION ===== -->
    @if($category->description)
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-bold"><i class="fas fa-align-left me-2 text-info"></i> Description</h6>
                <p class="mb-0">{{ $category->description }}</p>
            </div>
        </div>
    @endif

    <!-- ===== LISTE DES PRODUITS ===== -->
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="fas fa-box me-2 text-primary"></i> 
                Produits dans cette catégorie
                <span class="badge bg-primary ms-2">{{ $category->products()->count() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($category->products()->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nom du produit</th>
                                <th>Référence</th>
                                <th>Quantité</th>
                                <th>Prix</th>
                                <th>status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->products()->paginate(10) as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>
                                        <strong>{{ $product->nom ?? $product->name }}</strong>
                                    </td>
                                    <td>{{ $product->reference ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ $product->quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $product->quantity ?? 0 }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($product->price ?? 0, 2, ',', ' ') }} Fc</td>
                                    <td>
                                        <span class="badge {{ isset($product->status) && $product->status ? 'bg-success' : 'bg-secondary' }}">
                                            {{ isset($product->status) && $product->status ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-white">
                    {{ $category->products()->paginate(10)->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3 d-block"></i>
                    <h5>Aucun produit dans cette catégorie</h5>
                    <p class="text-muted">Commencez par ajouter des produits à cette catégorie</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection