@extends('layouts.stockadmin')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- ===== EN-TÊTE ===== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-plus-circle me-2 text-primary"></i> Nouveau produit
            </h4>
            <p class="text-muted mb-0 small">
                Ajoutez un nouveau produit à votre catalogue
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('stock.products.index') }}" class="btn btn-outline-secondary btn-sm">
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
                        <i class="fas fa-box me-2 text-primary"></i> Informations du produit
                    </h5>
                </div>

                <div class="card-body">

                    <form action="{{ route('stock.products.store') }}" method="POST" enctype="multipart/form-data">
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
                                            {{ $etab->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('etablissement_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ===== NOM ===== -->
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">
                                    Nom du produit <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ===== CODE ===== -->
                            <div class="col-md-4">
                                <label for="code" class="form-label fw-semibold">
                                    Code <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="code" 
                                       id="code" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code', $autoCode) }}"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ===== CODE-BARRES ===== -->
                            <div class="col-md-4">
                                <label for="barcode" class="form-label fw-semibold">
                                    Code-barres
                                </label>
                                <input type="text" 
                                       name="barcode" 
                                       id="barcode" 
                                       class="form-control @error('barcode') is-invalid @enderror" 
                                       value="{{ old('barcode') }}">
                                @error('barcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ===== CATÉGORIE ===== -->
                            <div class="col-md-4">
                                <label for="category_id" class="form-label fw-semibold">
                                    Catégorie <span class="text-danger">*</span>
                                </label>
                                <select name="category_id" 
                                        id="category_id" 
                                        class="form-select @error('category_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Sélectionnez une catégorie</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ===== UNITÉ ===== -->
                            <div class="col-md-4">
                                <label for="unit_id" class="form-label fw-semibold">
                                    Unité <span class="text-danger">*</span>
                                </label>
                                <select name="unit_id" 
                                        id="unit_id" 
                                        class="form-select @error('unit_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Sélectionnez une unité</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }} ({{ $unit->symbol ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ===== DESCRIPTION ===== -->
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold">
                                    Description
                                </label>
                                <textarea name="description" 
                                          id="description" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ===== IMAGE ===== -->
                            <div class="col-md-6">
                                <label for="image" class="form-label fw-semibold">
                                    Image du produit
                                </label>
                                <input type="file" 
                                       name="image" 
                                       id="image" 
                                       class="form-control @error('image') is-invalid @enderror" 
                                       accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format: JPG, PNG, GIF, WEBP. Max: 2MB</small>
                            </div>

                            <!-- ===== APERÇU IMAGE ===== -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Aperçu</label>
                                <div id="imagePreview" class="border rounded-3 p-2 text-center" style="min-height: 150px; background: #f8f9fa;">
                                    <i class="fas fa-image fa-3x text-muted mt-3"></i>
                                    <p class="text-muted small">Aucune image sélectionnée</p>
                                </div>
                            </div>

                            <!-- ===== STATUT ===== -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">Statut</label>
                                <div class="p-3 bg-light rounded-3">
                                    <div class="form-check form-switch">
                                        <input type="checkbox" 
                                               name="status" 
                                               id="status" 
                                               class="form-check-input" 
                                               value="1"
                                               {{ old('status', true) ? 'checked' : '' }}
                                               style="width: 3em; height: 1.5em;">
                                        <label class="form-check-label fw-semibold" for="status">
                                            <span class="badge {{ old('status', true) ? 'bg-success' : 'bg-danger' }}" id="statusBadge">
                                                {{ old('status', true) ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
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
                                    <a href="{{ route('stock.products.index') }}" class="btn btn-outline-danger ms-auto">
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

        // ===== APERÇU IMAGE =====
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');

        if (imageInput) {
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" style="max-height: 150px;">`;
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.innerHTML = `
                        <i class="fas fa-image fa-3x text-muted mt-3"></i>
                        <p class="text-muted small">Aucune image sélectionnée</p>
                    `;
                }
            });
        }

        // ===== TOGGLE STATUT =====
        const statusCheckbox = document.getElementById('status');
        const statusBadge = document.getElementById('statusBadge');

        if (statusCheckbox) {
            statusCheckbox.addEventListener('change', function() {
                const isActive = this.checked;
                statusBadge.textContent = isActive ? 'Actif' : 'Inactif';
                statusBadge.className = 'badge ' + (isActive ? 'bg-success' : 'bg-danger');
            });
        }

        // ===== AUTO-UPPERCASE CODE =====
        const codeInput = document.getElementById('code');
        if (codeInput) {
            codeInput.addEventListener('blur', function() {
                this.value = this.value.toUpperCase().trim();
            });
        }

        // ===== AUTO-FERMETURE DES ALERTES =====
        setTimeout(() => {
            document.querySelectorAll('.alert .btn-close').forEach(btn => btn?.click());
        }, 5000);

    });
</script>
@endsection