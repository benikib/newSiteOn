<div class="modal fade" id="editModal{{ $publicite->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $publicite->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-gradient-success">
                <h5 class="modal-title" id="editModalLabel{{ $publicite->id }}">Modifier la publicite</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form action="{{ route('publicites.update', $publicite->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="titre{{ $publicite->id }}" class="form-label">Titre</label>
                        <input type="text" class="form-control" id="titre{{ $publicite->id }}" name="titre" value="{{ $publicite->titre }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="description{{ $publicite->id }}" class="form-label">Description</label>
                        <textarea class="form-control" id="description{{ $publicite->id }}" name="description" rows="3" required>{{ $publicite->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="date{{ $publicite->id }}" class="form-label">Date </label>
                        <input type="date" class="form-control" id="date{{ $publicite->id }}" name="date" value="{{ \Carbon\Carbon::parse($publicite->date)->format('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="dure{{ $publicite->id }}" class="form-label">Durée (en jours)</label>
                        <input type="number" class="form-control" id="dure{{ $publicite->id }}" name="dure" value="{{ $publicite->dure }}" min="1" required>
                    </div>
                       <input type="hidden" name="status" value="active">
                    <div class="mb-3">
                        <label for="image{{ $publicite->id }}" class="form-label">Remplacer le média (facultatif)</label>
                        <input type="file" name="image" id="image{{ $publicite->id }}" class="form-control" accept="image/*,video/mp4,video/webm,video/quicktime">
                        <small class="text-muted">Images ou vidéos MP4, WebM, MOV (max. 50 Mo)</small>
                    </div>
                    <div class="mb-3">
                        <label for="etablissement_id{{ $publicite->id }}" class="form-label">Établissement</label>
                        <select class="form-select" id="etablissement_id{{ $publicite->id }}" name="etablissement_id" required>
                            <option value="" disabled selected>Choisir un établissement</option>
                            @foreach($etablissements as $etablissement)
                                <option value="{{ $etablissement->id }}" {{ $publicite->etablissement_id == $etablissement->id ? 'selected' : '' }}>
                                    {{ $etablissement->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>