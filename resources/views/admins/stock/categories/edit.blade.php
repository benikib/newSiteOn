@extends('layouts.stockadmin')

@section('content')
<div class="container-fluid px-4 py-3">

    <!-- ===== EN-TÊTE ===== -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="fas fa-edit me-2 text-warning"></i> Modifier la catégorie
            </h4>
            <p class="text-muted mb-0 small">
                Modifiez les informations de la catégorie "{{ $category->nom }}"
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('stock.categories.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
        </div>
    </div>

    <!-- ===== FORMULAIRE ===== -->
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">

            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-tag me-2 text-warning"></i> Informations de la catégorie
                    </h5>
                </div>

                <div class="card-body">

                    <form action="{{ route('stock.categories.update', $category) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- ===== NOM ===== -->
                        <div class="mb-4">
                            <label for="nom" class="form-label fw-semibold">
                                Nom de la catégorie <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-tag text-primary"></i>
                                </span>
                                <input type="text" 
                                       name="nom" 
                                       id="nom" 
                                       class="form-control @error('nom') is-invalid @enderror" 
                                       placeholder="Ex: Électronique, Vêtements, Alimentation..."
                                       value="{{ old('nom', $category->nom) }}"
                                       required
                                       autofocus>
                            </div>
                            @error('nom')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                </div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i> 
                                Le nom doit être unique et descriptif
                            </small>
                        </div>

                        <!-- ===== DESCRIPTION ===== -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">
                                Description
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-align-left text-info"></i>
                                </span>
                                <textarea name="description" 
                                          id="description" 
                                          class="form-control @error('description') is-invalid @enderror" 
                                          rows="4"
                                          placeholder="Décrivez cette catégorie...">{{ old('description', $category->description) }}</textarea>
                            </div>
                            @error('description')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                </div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i> 
                                Une description détaillée aide à mieux organiser vos produits
                            </small>
                        </div>

                        <!-- ===== status ===== -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">status</label>
                            <div class="p-3 bg-light rounded-3">
                                <div class="form-check form-switch">
                                    <input type="checkbox" 
                                           name="status" 
                                           id="status" 
                                           class="form-check-input @error('status') is-invalid @enderror" 
                                           value="1"
                                           {{ old('status', $category->status) ? 'checked' : '' }}
                                           style="width: 3em; height: 1.5em;">
                                    <label class="form-check-label fw-semibold" for="status" id="statusLabel">
                                        <span class="badge {{ old('status', $category->status) ? 'bg-success' : 'bg-danger' }}" id="statusBadge">
                                            {{ old('status', $category->status) ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Une catégorie active sera visible et utilisable pour les produits
                                </small>
                            </div>
                            @error('status')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- ===== BOUTONS ===== -->
                        <div class="d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Mettre à jour
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-1"></i> Réinitialiser
                            </button>
                            <a href="{{ route('stock.categories.index') }}" class="btn btn-outline-danger ms-auto">
                                <i class="fas fa-times me-1"></i> Annuler
                            </a>
                        </div>

                    </form>

                </div>
            </div>

            <!-- ===== INFORMATION PRODUITS ===== -->
            @if($category->products()->count() > 0)
                <div class="card border-info mt-4">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-box text-info fs-4 me-3 mt-1"></i>
                            <div>
                                <h6 class="fw-bold">Cette catégorie contient des produits</h6>
                                <p class="text-muted small mb-0">
                                    <strong>{{ $category->products()->count() }}</strong> produit(s) sont associés à cette catégorie.
                                    Si vous supprimez cette catégorie, les produits seront déplacés vers une autre catégorie.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ===== TOGGLE status =====
        const statusCheckbox = document.getElementById('status');
        const statusBadge = document.getElementById('statusBadge');
        const statusLabel = document.getElementById('statusLabel');

        if (statusCheckbox) {
            statusCheckbox.addEventListener('change', function() {
                const isActive = this.checked;
                statusBadge.textContent = isActive ? 'Actif' : 'Inactif';
                statusBadge.className = 'badge ' + (isActive ? 'bg-success' : 'bg-danger');
            });
        }

        // ===== AUTO-UPPERCASE PREMIÈRE LETTRE =====
        const nomInput = document.getElementById('nom');
        if (nomInput) {
            nomInput.addEventListener('blur', function() {
                if (this.value.length > 0) {
                    this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
                }
            });
        }

        // ===== VALIDATION EN TEMPS RÉEL =====
        const form = document.querySelector('form');
        const inputs = form?.querySelectorAll('input, textarea');

        inputs?.forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                const feedback = this.parentElement?.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.style.display = 'none';
                }
            });
        });

        // ===== AUTO-FERMETURE DES ALERTES =====
        setTimeout(() => {
            document.querySelectorAll('.alert .btn-close').forEach(btn => {
                btn?.click();
            });
        }, 5000);

    });
</script>
@endsection