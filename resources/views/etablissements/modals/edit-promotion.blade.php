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
                                    value="{{ $promotion->date_debut->format('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Date fin*</label>
                                <input type="date" name="date_fin" class="form-control"
                                    value="{{ $promotion->date_fin->format('Y-m-d') }}" required>
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
