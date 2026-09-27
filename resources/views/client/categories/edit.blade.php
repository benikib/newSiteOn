@extends('layouts.main')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-edit me-2 text-warning"></i> Modifier la catégorie
            </h4>
            <p class="text-muted mb-0 small">
                Modifiez les informations de la catégorie
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('client.categories.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card shadow-sm">
                <div class="card-body">

                    <form action="{{ route('client.categories.update', $category->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Nom -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" 
                                   value="{{ old('nom', $category->nom) }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="3">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="statut" class="form-check-input" id="statut" value="1" 
                                       {{ old('statut', $category->statut) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="statut">
                                    <span class="badge {{ old('statut', $category->statut) ? 'bg-success' : 'bg-danger' }}" id="statutBadge">
                                        {{ old('statut', $category->statut) ? 'Actif' : 'Inactif' }}
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Mettre à jour
                            </button>
                            <a href="{{ route('client.categories.index') }}" class="btn btn-outline-danger ms-auto">
                                <i class="fas fa-times me-1"></i> Annuler
                            </a>
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
    document.getElementById('statut')?.addEventListener('change', function() {
        const badge = document.getElementById('statutBadge');
        const isActive = this.checked;
        badge.textContent = isActive ? 'Actif' : 'Inactif';
        badge.className = 'badge ' + (isActive ? 'bg-success' : 'bg-danger');
    });
</script>
@endpush