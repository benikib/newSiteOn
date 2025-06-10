<div class="modal fade" id="addPubliciteModal{{ $etablissement->id }}" tabindex="-1"
    aria-labelledby="addPubliciteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-success">
                <h5 class="modal-title text-white" id="addPubliciteModalLabel">Nouvelle campagne publicitaire</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('publicite.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="etablissement_id" value="{{ $etablissement->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="titre" class="form-label">Titre</label>
                                <input type="text" class="form-control" id="titre" name="titre" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_debut" class="form-label">Date de début</label>
                                <input type="date" class="form-control" id="date_debut" name="date" required>
                            </div>
                            <div class="mb-3">
                                <label for="dure" class="form-label">Duree (jour)</label>
                                <input type="number" class="form-control" id="dure" name="dure" required>
                            </div>
                            {{-- <div class="mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="actif">Actif</option>
                                    <option value="inactif">Inactif</option>
                                    <option value="en_attente">En attente</option>
                                </select>
                            </div> --}}
                        </div>
                    </div>
                    {{-- <div class="mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*"
                            required>
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Créer la campagne</button>
                </div>
            </form>
        </div>
    </div>
</div>
