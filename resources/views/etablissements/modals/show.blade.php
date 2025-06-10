<div class="modal fade" id="etablissementInfoModal{{ $etablissement->id }}" tabindex="-1" role="dialog"
    aria-labelledby="etablissementInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-info">
                <h5 class="modal-title text-white" id="etablissementInfoModalLabel">
                    Détails de l'établissement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img src="{{ asset('assets/img/small-logos/logo-spotify.svg') }}"
                            class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px;" alt="logo">
                        <h4>{{ $etablissement->nom }}</h4>
                        <span class="badge bg-gradient-primary">{{ $etablissement->typeEtablissement->nom }}</span>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-3">Informations de base</h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span><i class="fas fa-phone me-2"></i>Téléphone</span>
                                        <span class="text-dark">{{ $etablissement->telephone }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span><i class="fas fa-map-marker-alt me-2"></i>Adresse</span>
                                        <span class="text-dark">{{ $etablissement->ville }},
                                            {{ $etablissement->commune }},
                                            {{ $etablissement->quartier }}, Av.
                                            {{ $etablissement->avenue }}
                                            N°{{ $etablissement->numero }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span><i class="fas fa-calendar-alt me-2"></i>Créé le</span>
                                        <span
                                            class="text-dark">{{ $etablissement->created_at->format('d/m/Y') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card mt-3">
                            <div class="card-body">
                                <h6 class="mb-3">Description</h6>
                                <p class="text-dark">{{ $etablissement->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
