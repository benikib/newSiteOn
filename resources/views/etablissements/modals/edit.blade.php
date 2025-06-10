<div class="modal fade" id="editEtablissementModal{{ $etablissement->id }}" tabindex="-1" role="dialog"
    aria-labelledby="editEtablissementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-warning">
                <h5 class="modal-title text-white" id="editEtablissementModalLabel">Modifier l'établissement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editEtablissementForm{{ $etablissement->id }}"
                action="{{ route('ets.update', $etablissement->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nom">Nom</label>
                                <input type="text" class="form-control" id="edit_nom" name="nom"
                                    value="{{ $etablissement->nom }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_type_etablissement_id">Type d'établissement</label>
                                <select class="form-control" id="edit_type_etablissement_id"
                                    name="type_etablissement_id" required>
                                    @foreach ($typeEtablissements as $type)
                                        <option value="{{ $type->id }}"
                                            {{ $etablissement->type_etablissement_id == $type->id ? 'selected' : '' }}>
                                            {{ $type->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_ville">Ville</label>
                                <input type="text" class="form-control" id="edit_ville" name="ville"
                                    value="{{ $etablissement->ville }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_commune">Commune</label>
                                <input type="text" class="form-control" id="edit_commune" name="commune"
                                    value="{{ $etablissement->commune }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_quartier">Quartier</label>
                                <input type="text" class="form-control" id="edit_quartier" name="quartier"
                                    value="{{ $etablissement->quartier }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_avenue">Avenue</label>
                                <input type="text" class="form-control" id="edit_avenue" name="avenue"
                                    value="{{ $etablissement->avenue }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_numero">Numéro</label>
                                <input type="text" class="form-control" id="edit_numero" name="numero"
                                    value="{{ $etablissement->numero }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_telephone">Téléphone</label>
                                <input type="text" class="form-control" id="edit_telephone" name="telephone"
                                    value="{{ $etablissement->telephone }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="edit_description">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3">{{ $etablissement->description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>
