<div class="modal fade" id="etablissementModal" tabindex="-1" role="dialog" aria-labelledby="etablissementModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h5 class="modal-title text-white" id="etablissementModalLabel">Ajouter un nouvel établissement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addEtablissementForm" action="{{ route('ets.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nom">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type_etablissement_id">Type d'établissement</label>
                                <select class="form-control" id="type_etablissement_id" name="type_etablissement_id"
                                    required>
                                    <option value="">Sélectionnez un type</option>
                                    @foreach ($typeEtablissements as $type)
                                        <option value="{{ $type->id }}">{{ $type->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ville">Ville</label>
                                <input type="text" class="form-control" id="ville" name="ville" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="commune">Commune</label>
                                <input type="text" class="form-control" id="commune" name="commune" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quartier">Quartier</label>
                                <input type="text" class="form-control" id="quartier" name="quartier" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="avenue">Avenue</label>
                                <input type="text" class="form-control" id="avenue" name="avenue" required>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="numero">Numéro</label>
                                <input type="text" class="form-control" id="numero" name="numero" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telephone">Téléphone</label>
                                <input type="text" class="form-control" id="telephone" name="telephone" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
