@extends('layouts.main')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-edit me-2 text-warning"></i> Modifier le produit
            </h4>
            <p class="text-muted mb-0 small">
                Modifiez les informations du produit "{{ $product->name ?? '' }}"
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('client.products.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2 text-warning"></i> Informations du produit
                    </h5>
                </div>

                <div class="card-body">

                    <form action="{{ route('client.products.update', $product->id ?? 0) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Établissement (lecture seule) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Établissement</label>
                            <p class="fw-bold">{{ $product->etablissement->name ?? 'N/A' }}</p>
                            <input type="hidden" name="etablissement_id" value="{{ $product->etablissement_id ?? '' }}">
                        </div>

                        <div class="row g-3">

                            <!-- ===== NOM ===== -->
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">
                                    Nom du produit <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $product->name ?? '') }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ===== CODE ===== -->
                            <div class="col-md-3">
                                <label for="code" class="form-label fw-semibold">
                                    Code <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="code" 
                                       id="code" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code', $product->code ?? '') }}"
                                       required>
                                @error('code')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ===== CODE-BARRES ===== -->
                            <div class="col-md-3">
                                <label for="barcode" class="form-label fw-semibold">
                                    Code-barres
                                </label>
                                <input type="text" 
                                       name="barcode" 
                                       id="barcode" 
                                       class="form-control @error('barcode') is-invalid @enderror" 
                                       value="{{ old('barcode', $product->barcode ?? '') }}">
                                @error('barcode')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ===== CATÉGORIE ===== -->
                            <div class="col-md-6">
                                <label for="category_id" class="form-label fw-semibold">
                                    Catégorie <span class="text-danger">*</span>
                                </label>
                                <select name="category_id" 
                                        id="category_id" 
                                        class="form-select @error('category_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Sélectionnez une catégorie</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                            {{ $category->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- ===== UNITÉ ===== -->
                            <div class="col-md-6">
                                <label for="unit_id" class="form-label fw-semibold">
                                    Unité <span class="text-danger">*</span>
                                </label>
                                <select name="unit_id" 
                                        id="unit_id" 
                                        class="form-select @error('unit_id') is-invalid @enderror" 
                                        required>
                                    <option value="">Sélectionnez une unité</option>
                                    @foreach($units ?? [] as $unit)
                                        <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }} ({{ $unit->symbol ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
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
                                          rows="3">{{ old('description', $product->description ?? '') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
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
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Format: JPG, PNG, GIF, WEBP. Max: 2MB</small>
                                @if(isset($product) && $product->image)
                                    <div class="mt-2">
                                        <span class="badge bg-info">Image actuelle</span>
                                    </div>
                                @endif
                            </div>

                            <!-- ===== APERÇU IMAGE ===== -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Aperçu</label>
                                <div id="imagePreview" class="border rounded-3 p-2 text-center" style="min-height: 150px; background: #f8f9fa;">
                                    @if(isset($product) && $product->image)
                                        <img src="{{ $product->image_url ?? asset('storage/' . $product->image) }}" class="img-fluid rounded" style="max-height: 150px;">
                                    @else
                                        <i class="fas fa-image fa-3x text-muted mt-3"></i>
                                        <p class="text-muted small">Aucune image</p>
                                    @endif
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
                                               {{ old('status', $product->status ?? true) ? 'checked' : '' }}
                                               style="width: 3em; height: 1.5em;">
                                        <label class="form-check-label fw-semibold" for="status">
                                            <span class="badge {{ old('status', $product->status ?? true) ? 'bg-success' : 'bg-danger' }}" id="statusBadge">
                                                {{ old('status', $product->status ?? true) ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- ===== BOUTONS ===== -->
                            <div class="col-12">
                                <div class="d-flex gap-2 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i> Mettre à jour
                                    </button>
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo me-1"></i> Réinitialiser
                                    </button>
                                    <a href="{{ route('client.products.index') }}" class="btn btn-outline-danger ms-auto">
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

@push('scripts')
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

        // ===== AUTO-UPPERCASE PREMIÈRE LETTRE NOM =====
        const nameInput = document.getElementById('name');
        if (nameInput) {
            nameInput.addEventListener('blur', function() {
                if (this.value.length > 0) {
                    this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                }
            });
        }

    });
</script>
@endpush