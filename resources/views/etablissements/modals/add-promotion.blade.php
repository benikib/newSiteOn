<div class="modal fade" id="addPromotionToServiceModal{{ $service->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-success text-white">
                <h5 class="modal-title">Nouvelle promotion pour {{ $service->nom }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('promotions.store') }}" method="POST">
                @csrf
                <input type="hidden" name="service_id" value="{{ $service->id }}">
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Titre*</label>
                        <input type="text" name="titre" class="form-control" required maxlength="100">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Prix*</label>
                            <div class="input-group">
                                <input type="number" name="prix" class="form-control" step="0.01" min="0" required>
                                <span class="input-group-text">€</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Statut*</label>
                            <select name="statut" class="form-select" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="planned">Planifiée</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label">Date début*</label>
                            <input type="date" name="date_debut" class="form-control" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Date fin*</label>
                            <input type="date" name="date_fin" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>