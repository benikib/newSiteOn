<div class="modal fade" id="galleryModal{{ $etablissement->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary">
                <h5 class="modal-title text-white">Galerie - {{ $etablissement->nom }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#all-{{ $etablissement->id }}">Tous</a>
                    </li>
                    @foreach ($etablissement->services as $service)
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#service-{{ $service->id }}">
                                {{ $service->nom }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content">
                    <!-- Toutes les photos - Version défilable -->
                    <div class="tab-pane fade show active" id="all-{{ $etablissement->id }}">
                        @if ($etablissement->photos->count() > 0)
                            <div class="horizontal-scroll-wrapper">
                                <div class="horizontal-scroll-container">
                                    @foreach ($etablissement->photos as $photo)
                                        <div class="scroll-item">
                                            <div class="card h-100">
                                                <div class="image-container" style="height: 180px; overflow: hidden;">
                                                    <img src="{{ asset('storage/' . $photo->image_path) }}"
                                                        class="img-fluid w-100 h-100 object-fit-cover"
                                                        alt="{{ $photo->titre }}"
                                                        onerror="this.src='/placeholder.jpg';">
                                                </div>
                                                <div class="card-body">
                                                    <h6 class="card-title">{{ $photo->titre }}</h6>
                                                    <p class="card-text small text-muted">{{ $photo->description }}</p>
                                                </div>
                                                <div class="card-footer bg-transparent">
                                                    <form action="{{ route('galleries.destroy', $photo->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Supprimer cette photo?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">Aucune photo disponible</div>
                        @endif
                    </div>

                    <!-- Photos par service - Version défilable -->
                    @foreach ($etablissement->services as $service)
                        <div class="tab-pane fade" id="service-{{ $service->id }}">
                            @if ($service->photos->count() > 0)
                                <div class="horizontal-scroll-wrapper">
                                    <div class="horizontal-scroll-container">
                                        @foreach ($service->photos as $photo)
                                            <div class="scroll-item">
                                                <div class="card h-100">
                                                    <div class="image-container"
                                                        style="height: 180px; overflow: hidden;">

                                                        <img src="{{ asset('storage/' . $photo->image_path) }}"
                                                            class="img-fluid w-100 h-100 object-fit-cover"
                                                            alt="{{ $photo->titre }}">
                                                    </div>
                                                    <div class="card-body">
                                                        <h6 class="card-title">{{ $photo->titre }}</h6>
                                                        <p class="card-text small text-muted">{{ $photo->description }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">Aucune photo pour ce service</div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <style>
                    /* Styles pour le défilement horizontal */
                    .horizontal-scroll-wrapper {
                        width: 100%;
                        overflow-x: auto;
                        white-space: nowrap;
                        -webkit-overflow-scrolling: touch;
                        padding-bottom: 15px;
                        margin: -5px;
                        /* Compensation pour l'espacement */
                    }

                    .horizontal-scroll-container {
                        display: inline-flex;
                        gap: 15px;
                        padding: 5px;
                        /* Espacement interne */
                    }

                    .scroll-item {
                        display: inline-block;
                        width: 240px;
                        flex: 0 0 auto;
                    }

                    /* Contrôle de l'image */
                    .image-container {
                        height: 180px;
                        overflow: hidden;
                    }

                    .object-fit-cover {
                        object-fit: cover;
                        object-position: center;
                        transition: transform 0.3s ease;
                    }

                    .object-fit-cover:hover {
                        transform: scale(1.03);
                    }

                    /* Style personnalisé pour la scrollbar */
                    .horizontal-scroll-wrapper::-webkit-scrollbar {
                        height: 8px;
                    }

                    .horizontal-scroll-wrapper::-webkit-scrollbar-track {
                        background: #f1f1f1;
                        border-radius: 10px;
                    }

                    .horizontal-scroll-wrapper::-webkit-scrollbar-thumb {
                        background: #888;
                        border-radius: 10px;
                    }
                </style>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                    data-bs-target="#addPhotoModal{{ $etablissement->id }}">
                    <i class="fas fa-plus"></i> Ajouter une photo
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'ajout de photo -->
<!-- Modal d'ajout de photo -->
<div class="modal fade" id="addPhotoModal{{ $etablissement->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-success">
                <h5 class="modal-title text-white">Ajouter une photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('galleries.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="etablissement_id" value="{{ $etablissement->id }}">
                <div class="modal-body">
                    <!-- Sélection du service -->
                    <div class="mb-3">
                        <label class="form-label">Service (optionnel)</label>
                        <select name="service_id" class="form-select">
                            <option value="">Aucun service spécifique</option>
                            @foreach ($etablissement->services as $service)
                                <option value="{{ $service->id }}">{{ $service->nom }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- <!-- Sélection de la promotion -->
                    <div class="mb-3">
                        <label class="form-label">Promotion (optionnel)</label>
                        <select name="promotion_id" class="form-select">
                            <option value="">Aucune promotion spécifique</option>
                            @foreach ($se->promotions as $promotion)
                                <option value="{{ $promotion->id }}">{{ $promotion->nom }}</option>
                            @endforeach
                        </select>
                    </div> --}}

                    <!-- Champs existants -->
                    <div class="mb-3">
                        <label class="form-label">Titre</label>
                        <input type="text" name="titre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                        <small class="text-muted">Formats acceptés: JPG, PNG, GIF (Max: 2MB)</small>
                    </div>
                    <input type="hidden" name="etablissement_id" value="{{ $etablissement->id }}">
                    <input type="hidden" name="status" value="active">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
