@foreach($services as $service)
<div class="modal fade" id="editModal{{ $service->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $service->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel{{ $service->id }}">Modifier le Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form action="{{ route('services.update', $service->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <!-- Onglets de navigation -->
                    <ul class="nav nav-tabs mb-4" id="editTabs{{ $service->id }}" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="edit-service-tab-{{ $service->id }}" data-bs-toggle="tab" data-bs-target="#edit-service-{{ $service->id }}" type="button" role="tab" aria-controls="edit-service" aria-selected="true">Service</button>
                        </li>
                    </ul>

                    <!-- Contenu des onglets -->
                    <div class="tab-content" id="editTabsContent{{ $service->id }}">
                        <!-- Onglet Service -->
                        <div class="tab-pane fade show active" id="edit-service-{{ $service->id }}" role="tabpanel" aria-labelledby="edit-service-tab-{{ $service->id }}">
                            <div class="mb-3">
                                <label for="edit_nom{{ $service->id }}" class="form-label">Nom du Service*</label>
                                <input type="text" name="nom" id="edit_nom{{ $service->id }}" 
                                       class="form-control" value="{{ $service->nom }}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="edit_description{{ $service->id }}" class="form-label">Description</label>
                                <textarea name="description" id="edit_description{{ $service->id }}" 
                                          class="form-control" rows="3">{{ $service->description }}</textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label for="edit_prix{{ $service->id }}" class="form-label">Prix*</label>
                                <input type="number" name="prix" id="edit_prix{{ $service->id }}" 
                                       class="form-control" value="{{ $service->prix }}" required>
                            </div>

                            <input type="hidden" name="etablissement_id" value="{{ $service->etablissement_id }}">

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


@endforeach