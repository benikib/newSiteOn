@extends('layouts.stockadmin')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- ===== EN-TÊTE ===== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-box me-2 text-primary"></i> {{ $product->name }}
            </h4>
            <p class="text-muted mb-0 small">
                Détails du produit
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('stock.products.edit', $product) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit me-1"></i> Modifier
            </a>
            <a href="{{ route('stock.products.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <!-- ===== DÉTAILS ===== -->
    <div class="row g-3">

        <!-- Image -->
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <img src="{{ $product->image_url }}" 
                         alt="{{ $product->name }}" 
                         class="img-fluid rounded" 
                         style="max-height: 300px; width: 100%; object-fit: contain;">
                </div>
            </div>
        </div>

        <!-- Informations -->
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Code</label>
                            <p class="fw-semibold"><span class="badge bg-secondary">{{ $product->code }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Code-barres</label>
                            <p class="fw-semibold">{{ $product->barcode ?? 'Non défini' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Catégorie</label>
                            <p class="fw-semibold">{{ $product->category->nom ?? 'Non catégorisé' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Unité</label>
                            <p class="fw-semibold">{{ $product->unit->name ?? 'Non défini' }}</p>
                        </div>
                        <div class="col-md-12">
                            <label class="text-muted small">Description</label>
                            <p class="fw-semibold">{{ $product->description ?? 'Aucune description' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">status</label>
                            <p>
                                <span class="badge {{ $product->status ? 'bg-success' : 'bg-danger' }}">
                                    {{ $product->status ? 'Actif' : 'Inactif' }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Créé le</label>
                            <p class="fw-semibold">{{ $product->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection