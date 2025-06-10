<div class="modal fade" id="editPubliciteModal{{ $publicite->id }}" tabindex="-1" aria-labelledby="editPubliciteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h5 class="modal-title text-white" id="editPubliciteModalLabel">Modifier la campagne publicitaire</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('publicite.update', $publicite->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="etablissement_id" value="{{ $etablissement->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_titre" class="form-label">Titre</label>
                                <input type="text" class="form-control" id="edit_titre" name="titre"
                                    value="{{ old('titre', $publicite->titre) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_description" class="form-label">Description</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="3" required>{{ old('description', $publicite->description) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_date_debut" class="form-label">Date de début
                                    {{ $publicite->date }}</label>
                                <input type="date" class="form-control" id="edit_date_debut" name="date_debut"
                                    value="{{ old('date_debut', $publicite->date ? $publicite->date : '') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="edit_date_fin" class="form-label">Durée{{ $publicite->dure }}</label>
                                <input type="text" class="form-control" id="edit_date_fin" name="dure"
                                    value="{{ old('date_fin', $publicite->dure ? $publicite->dure : '') }}" required>
                            </div>
                            {{-- <div class="mb-3">
                                <label for="edit_statut" class="form-label">Statut</label>
                                <select class="form-select" id="edit_statut" name="statut" required>
                                    <option value="actif" {{ $publicite->statut == 'actif' ? 'selected' : '' }}>Actif
                                    </option>
                                    <option value="inactif" {{ $publicite->statut == 'inactif' ? 'selected' : '' }}>
                                        Inactif</option>
                                    <option value="en_attente"
                                        {{ $publicite->statut == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                </select>
                            </div> --}}
                        </div>
                    </div>
                    {{-- <div class="mb-3">
                        <label for="edit_image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                        @if ($publicite->image)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $publicite->image) }}" alt="Image actuelle"
                                    style="max-height: 100px;">
                                <small class="text-muted">Image actuelle</small>
                            </div>
                        @endif
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>
