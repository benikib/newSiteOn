<div class="modal fade" id="editServiceModal{{ $service->id }}" tabindex="-1" aria-labelledby="editServiceModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-warning">
                <h5 class="modal-title text-white" id="editServiceModalLabel">Modifier le service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('services.update', $service->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nom{{ $service->id }}" class="form-label">Nom du service</label>
                        <input type="text" class="form-control" id="edit_nom{{ $service->id }}" name="nom"
                            value="{{ $service->nom }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description{{ $service->id }}" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description{{ $service->id }}" name="description" rows="3">{{ $service->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_prix{{ $service->id }}" class="form-label">Prix (FCFA)</label>
                        <input type="number" class="form-control" id="edit_prix{{ $service->id }}" name="prix"
                            value="{{ $service->prix }}" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
