<!-- Modale principale de gestion des promotions -->
<div class="modal fade" id="servicePromotionsModal{{ $service->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-tag me-2"></i>
                    Promotions pour: {{ $service->nom }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Bouton pour ajouter une promotion -->
                <button class="btn btn-success mb-3" data-bs-toggle="modal"
                    data-bs-target="#addPromotionToServiceModal{{ $service->id }}">
                    <i class="fas fa-plus-circle me-1"></i> Ajouter promotion
                </button>

                <!-- Liste des promotions existantes -->
                <div class="row g-3" id="promotions-list-{{ $service->id }}">
                    @forelse($service->promotions as $promotion)
                        <div class="col-md-6">
                            <div class="card promotion-card h-100 border-start border-4 border-primary">
                                <div class="card-header d-flex justify-content-between align-items-center bg-light">
                                    <h6 class="mb-0">{{ $promotion->titre }}</h6>
                                    <span
                                        class="badge bg-{{ $promotion->statut === 'active' ? 'success' : 'warning' }}">
                                        {{ $promotion->statut }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">{{ $promotion->description }}</p>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <span class="text-primary fw-bold">
                                            {{ number_format($promotion->prix, 2) }} Fc
                                        </span>
                                        <small class="text-muted">
                                            {{ $promotion->date_debut }} -
                                            {{ $promotion->date_fin }}
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#editPromotionModal{{ $promotion->id }}">
                                            <i class="fas fa-edit"></i> Modifier
                                        </button>
                                        <form action="{{ route('promotions.destroy', $promotion->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Supprimer cette promotion?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center py-4">
                                <i class="fas fa-info-circle fa-2x mb-3"></i>
                                <h5 class="mb-1">Aucune promotion disponible</h5>
                                <p class="mb-0">Ajoutez votre première promotion pour ce service</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modale d'édition - À PLACER APRÈS LA MODALE PRINCIPALE, EN DEHORS DE TOUTE BOUCLE -->
@foreach ($service->promotions as $promotion)
    <div class="modal fade" id="editPromotionModal{{ $promotion->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-gradient-warning text-white">
                    <h5 class="modal-title">Modifier promotion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('promotions.update', $promotion->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Titre*</label>
                            <input type="text" name="titre" class="form-control" value="{{ $promotion->titre }}"
                                required maxlength="100">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $promotion->description }}</textarea>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Prix*</label>
                                <div class="input-group">
                                    <input type="number" name="prix" class="form-control" step="0.01"
                                        min="0" value="{{ $promotion->prix }}" required>
                                    <span class="input-group-text">Fc</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Statut*</label>
                                <select name="statut" class="form-select" required>
                                    <option value="active" {{ $promotion->statut === 'active' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="inactive" {{ $promotion->statut === 'inactive' ? 'selected' : '' }}>
                                        Inactive</option>
                                    <option value="planned" {{ $promotion->statut === 'planned' ? 'selected' : '' }}>
                                        Planifiée</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                                <label class="form-label">Date début*</label>
                                <input type="date" name="date_debut" class="form-control"
                                    value="{{ $promotion->date_debut }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Date fin*</label>
                                <input type="date" name="date_fin" class="form-control"
                                    value="{{ $promotion->date_fin }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i> Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Vérifie que Bootstrap est bien chargé
        if (typeof bootstrap === 'undefined') {
            console.error('Bootstrap 5 non chargé!');
            return;
        }

        // Écouteur pour les boutons d'édition
        document.querySelectorAll('[data-bs-target^="#editPromotionModal"]').forEach(btn => {
            btn.addEventListener('click', function() {
                const target = this.getAttribute('data-bs-target');
                console.log('Tentative d\'ouverture de:', target);

                const modalElement = document.querySelector(target);
                if (!modalElement) {
                    console.error('Modale introuvable:', target);
                    return;
                }

                const modal = new bootstrap.Modal(modalElement);
                modal.show();
            });
        });
    });
</script>
