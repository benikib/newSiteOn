@foreach($typeEtablissements as $type)
<div class="modal fade" id="editModal{{ $type->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $type->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel{{ $type->id }}">Modifier le type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <form action="{{ route('type_etablissements.update', $type->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name{{ $type->id }}" class="form-label">Nom du Type d'Etablissement</label>
                        <input type="text" name="nom" id="edit_name{{ $type->id }}" 
                               class="form-control" value="{{ $type->nom }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description{{ $type->id }}" class="form-label">Description</label>
                        <textarea name="description" id="edit_description{{ $type->id }}" 
                                  class="form-control" rows="3">{{ $type->description }}</textarea>
                    </div>

                    <div id="statusMessage" class="alert alert-danger d-none" role="alert">
                        <p class="mb-0"></p>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach