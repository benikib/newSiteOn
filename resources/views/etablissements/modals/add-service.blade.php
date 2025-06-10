<div class="modal fade" id="addServiceModal{{ $etablissement->id }}" tabindex="-1" aria-labelledby="addServiceModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-success">
                <h5 class="modal-title text-white" id="addServiceModalLabel">Ajouter un service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('service.store') }}" method="POST">
                @csrf
                <input type="hidden" name="etablissement_id" value="{{ $etablissement->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom du service</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="prix" class="form-label">Prix ($)</label>
                        <input type="number" class="form-control" id="prix" name="prix" min="0"
                            required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
