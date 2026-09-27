@extends('layouts.stockadmin')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- ===== EN-TÊTE ===== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-plus-circle me-2 text-primary"></i> Ajouter un stock
            </h4>
            <p class="text-muted mb-0 small">
                Ajoutez un nouveau stock pour un établissement
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('admin.stocks.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <!-- ===== FORMULAIRE ===== -->
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-warehouse me-2 text-primary"></i> Informations du stock
                    </h5>
                </div>

                <div class="card-body">

                    <form action="{{ route('admin.stocks.store') }}" method="POST">
                        @csrf

                        <div class="row g-3">

                            <!-- ===== ÉTABLISSEMENT ===== -->
                            <div class="col-md-6">
                                <label for="etablissement_id" class="form-label fw-semibold">
                                    Établissement <span class="text-danger">*</span>
                                </label>
                                <select name="etablissement_id" 
                                        id="etablissement_id" 
                                        class="form-select @error('etablissement_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Sélectionnez un établissement</option>
                                    @foreach($etablissements as $etab)
                                        <option value="{{ $etab->id }}" {{ old('etablissement_id') == $etab->id ? 'selected' : '' }}>
                                            {{ $etab->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('etablissement_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ===== PRODUIT ===== -->
                            <div class="col-md-6">
                                <label for="product_id" class="form-label fw-semibold">
                                    Produit <span class="text-danger">*</span>
                                </label>
                                <select name="product_id" 
                                        id="product_id" 
                                        class="form-select @error('product_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Sélectionnez un produit</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->name }} ({{ $product->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ===== QUANTITÉ ===== -->
                            <div class="col-md-3">
                                <label for="quantity" class="form-label fw-semibold">
                                    Quantité <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       name="quantity" 
                                       id="quantity" 
                                       class="form-control @error('quantity') is-invalid @enderror" 
                                       value="{{ old('quantity', 0) }}"
                                       min="0"
                                       required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ===== STOCK MINIMUM ===== -->
                            <div class="col-md-3">
                                <label for="minimum_stock" class="form-label fw-semibold">
                                    Stock minimum
                                </label>
                                <input type="number" 
                                       name="minimum_stock" 
                                       id="minimum_stock" 
                                       class="form-control @error('minimum_stock') is-invalid @enderror" 
                                       value="{{ old('minimum_stock', 0) }}"
                                       min="0">
                                @error('minimum_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Alerte si quantité ≤ ce seuil</small>
                            </div>

                            <!-- ===== PRIX D'ACHAT ===== -->
                            <div class="col-md-3">
                                <label for="purchase_price" class="form-label fw-semibold">
                                    Prix d'achat <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Fc</span>
                                    <input type="number" 
                                           name="purchase_price" 
                                           id="purchase_price" 
                                           class="form-control @error('purchase_price') is-invalid @enderror" 
                                           value="{{ old('purchase_price', 0) }}"
                                           step="0.01"
                                           min="0"
                                           required>
                                </div>
                                @error('purchase_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ===== PRIX DE VENTE ===== -->
                            <div class="col-md-3">
                                <label for="selling_price" class="form-label fw-semibold">
                                    Prix de vente <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Fc</span>
                                    <input type="number" 
                                           name="selling_price" 
                                           id="selling_price" 
                                           class="form-control @error('selling_price') is-invalid @enderror" 
                                           value="{{ old('selling_price', 0) }}"
                                           step="0.01"
                                           min="0"
                                           required>
                                </div>
                                @error('selling_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ===== BOUTONS ===== -->
                            <div class="col-12">
                                <div class="d-flex gap-2 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i> Enregistrer
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo me-1"></i> Réinitialiser
                                    </button>
                                    <a href="{{ route('admin.stocks.index') }}" class="btn btn-outline-danger ms-auto">
                                        <i class="fas fa-times me-1"></i> Annuler
                                    </a>
                                </div>
                            </div>

                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-fermeture des alertes
        setTimeout(() => {
            document.querySelectorAll('.alert .btn-close').forEach(btn => btn?.click());
        }, 5000);
    });
</script>
@endsection