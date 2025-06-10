<!-- Modal des Services -->
<div class="modal fade" id="serviceModal{{ $etablissement->id }}" tabindex="-1" role="dialog"
    aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h5 class="modal-title text-white" id="serviceModalLabel">Services de {{ $etablissement->nom }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6>Liste des services</h6>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                        data-bs-target="#addServiceModal{{ $etablissement->id }}">
                        <i class="fas fa-plus me-1"></i> Ajouter
                    </button>
                </div>

                @if ($etablissement->services->count() > 0)
                    <div class="list-group">
                        @foreach ($etablissement->services as $service)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $service->nom }}</h6>
                                        <p class="mb-1 text-muted">{{ Str::limit($service->description, 50) }}</p>
                                        <small>{{ number_format($service->prix, 0, ',', ' ') }} FCFA</small>
                                    </div>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                            data-bs-target="#editServiceModal{{ $service->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#servicePromotionsModal{{ $service->id }}"
                                            title="Gérer les promotions">
                                            <i class="fas fa-tag me-1"></i> Promotions
                                        </button>
                                        <form action="{{ route('services.destroy', $service->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce service?')">
                                                <i class="fas fa-trash"></i>
                                            </button>

                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        Aucun service disponible pour cet établissement.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'ajout de service -->
<div class="modal fade" id="addServiceModal{{ $etablissement->id }}" tabindex="-1"
    aria-labelledby="addServiceModalLabel" aria-hidden="true">
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
                        <label for="prix" class="form-label">Prix (FCFA)</label>
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
@include('etablissements.modals.promotions', ['etablissement' => $etablissement])
<!-- Modals d'édition de service -->
@foreach ($etablissement->services as $service)
    <div class="modal fade" id="editServiceModal{{ $service->id }}" tabindex="-1"
        aria-labelledby="editServiceModalLabel" aria-hidden="true">
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
                            <input type="text" class="form-control" id="edit_nom{{ $service->id }}"
                                name="nom" value="{{ $service->nom }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description{{ $service->id }}" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description{{ $service->id }}" name="description" rows="3">{{ $service->description }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_prix{{ $service->id }}" class="form-label">Prix (FCFA)</label>
                            <input type="number" class="form-control" id="edit_prix{{ $service->id }}"
                                name="prix" value="{{ $service->prix }}" min="0" required>
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
@endforeach
