@extends('layouts.main')

@section('content')
<div class="container-fluid px-4 py-3">

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-plus-circle me-2 text-primary"></i> Nouvelle catégorie
            </h4>
            <p class="text-muted mb-0 small">
                Créez une nouvelle catégorie de produits
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

                    <form action="{{ route('client.categories.store') }}" method="POST">
                        @csrf

                        <!-- Établissement -->
                        @if(isset($etablissements) && $etablissements->count() > 1)
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Établissement <span class="text-danger">*</span></label>
                                <select name="etablissement_id" class="form-select @error('etablissement_id') is-invalid @enderror" required>
                                    <option value="">Sélectionnez un établissement</option>
                                    @foreach($etablissements as $etab)
                                        <option value="{{ $etab->id }}" {{ old('etablissement_id', $selectedEtablissementId ?? '') == $etab->id ? 'selected' : '' }}>
                                            {{ $etab->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('etablissement_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @elseif(isset($selectedEtablissementId))
                            <input type="hidden" name="etablissement_id" value="{{ $selectedEtablissementId }}">
                        @endif

                        <!-- Nom -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom" class="form-control @error('nom') is-invalid @enderror" 
                                   value="{{ old('nom') }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="statut" class="form-check-input" id="statut" value="1" 
                                       {{ old('statut', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="statut">
                                    <span class="badge {{ old('statut', true) ? 'bg-success' : 'bg-danger' }}" id="statutBadge">
                                        {{ old('statut', true) ? 'Actif' : 'Inactif' }}
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Enregistrer
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i> Réinitialiser
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