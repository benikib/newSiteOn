@extends('layouts.base')
@section('title', 'Etablissement - ' . $etablissement->nom)
@section('content')
    <style>
        /* Styles personnalisés */
        .profile-card {
            border-radius: 1rem;
            overflow: hidden;
            position: relative;
        }

        .profile-img {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border: 5px solid white;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .profile-img:hover {
            transform: scale(1.05);
        }

        .edit-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
        }

        .section-title {
            border-left: 4px solid #0d6efd;
            padding-left: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .add-btn {
            font-size: 0.8rem;
        }

        /* Galerie styles */
        .gallery-item {
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .gallery-item:hover .gallery-actions {
            opacity: 1;
        }

        .gallery-actions {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            padding: 10px;
            display: flex;
            justify-content: center;
            gap: 10px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        /* Modal styles */
        .modal-dialog-custom {
            max-width: 800px;
        }

        /* Drag and drop styles */
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 5px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .upload-area:hover {
            border-color: #0d6efd;
            background: rgba(13, 110, 253, 0.05);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .profile-img {
                width: 120px;
                height: 120px;
            }
        }
    </style>

    <div class="container py-4 py-lg-5">


        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="profile-card bg-white shadow-sm p-3 p-md-4">
                    <!-- Edit Button for Profile -->
                    <button class="btn btn-sm btn-primary edit-btn" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fas fa-edit"></i> Modifier
                    </button>

                    <div class="row g-4">
                        <!-- Left Column (Photo + Contact) -->
                        <div class="col-md-4 text-center text-md-start">
                            <div class="position-relative mb-4">
                                @if ($etablissement->photos->isNotEmpty())
                                    <img src="{{ asset('storage/' . str_replace('public/', '', $etablissement->photos->first()->image_path)) }}"
                                        class="profile-img rounded-circle shadow"
                                        alt="{{ $etablissement->photos->first()->titre }}" data-bs-toggle="modal"
                                        data-bs-target="#changePhotoModal" onerror="this.src='/placeholder.jpg';">
                                @else
                                    <img src="/placeholder.jpg" class="profile-img rounded-circle shadow"
                                        alt="Photo par défaut" data-bs-toggle="modal" data-bs-target="#changePhotoModal" />
                                @endif
                            </div>

                            <div class="contact-box rounded p-3 p-md-4 mb-3 position-relative">
                                <button class="btn btn-sm btn-outline-primary position-absolute top-0 end-0 m-2"
                                    data-bs-toggle="modal" data-bs-target="#editContactModal">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <h5 class="text-center mb-3"><i class="fas fa-phone-alt me-2"></i> Contact</h5>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-phone text-primary me-2"></i>
                                    <span>{{ $etablissement->telephone ?? 'Non renseigné' }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-envelope text-primary me-2"></i>
                                    <span>{{ $etablissement->email ?? 'Non renseigné' }}</span>
                                </div>
                                @if ($etablissement->website)
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-globe text-primary me-2"></i>
                                        <a href="{{ $etablissement->website }}" target="_blank"
                                            class="text-decoration-none text-primary text-truncate">
                                            {{ $etablissement->website }}
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <!-- Social Media -->
                            <div class="d-flex justify-content-center gap-3 position-relative">
                                <button class="btn btn-sm btn-outline-primary position-absolute top-0 end-0"
                                    data-bs-toggle="modal" data-bs-target="#editSocialModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="#" class="text-primary"><i class="fab fa-facebook-f fa-lg"></i></a>
                                <a href="#" class="text-primary"><i class="fab fa-twitter fa-lg"></i></a>
                                <a href="#" class="text-primary"><i class="fab fa-instagram fa-lg"></i></a>
                            </div>
                        </div>

                        <!-- Right Column (Info + Services + Address) -->
                        <div class="col-md-8">
                            <h1 class="h2 mb-3">{{ $etablissement->nom }}</h1>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <p class="description-text text-muted mb-0">{{ $etablissement->description }}</p>
                                <button class="btn btn-sm btn-outline-primary ms-3" data-bs-toggle="modal"
                                    data-bs-target="#editDescriptionModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>

                            <!-- Services Section -->
                            <div class="mb-4">
                                <h4 class="section-title h5 mb-3">
                                    Tarifs
                                    <button class="btn btn-sm btn-primary add-btn" data-bs-toggle="modal"
                                        data-bs-target="#addServiceModal">
                                        <i class="fas fa-plus"></i> Ajouter
                                    </button>
                                </h4>

                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Type</th>
                                                <th>Description</th>
                                                <th>Prix/jour</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($etablissement->services as $service)
                                                <tr>
                                                    <td>{{ $service->nom }}</td>
                                                    <td>{{ $service->description ? Str::limit($service->description, 50, '...') : 'Aucune description' }}
                                                    </td>
                                                    <td>{{ number_format($service->prix, 2) }} $</td>
                                                    <td>
                                                        <div class="d-flex gap-2">
                                                            <button class="btn btn-sm btn-outline-primary"
                                                                data-bs-toggle="modal" data-bs-target="#editServiceModal"
                                                                data-service-id="{{ $service->id }}"
                                                                data-service-name="{{ $service->nom }}"
                                                                data-service-desc="{{ $service->description }}"
                                                                data-service-price="{{ $service->prix }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <form
                                                                action="{{ route('etablissements.services.destroy', $service->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                    onclick="return confirm('Êtes-vous sûr ?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">Aucun service
                                                        disponible</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Address Section -->
                            <div class="row g-4">
                                <div class="col-md-6 position-relative">
                                    <button class="btn btn-sm btn-outline-primary position-absolute top-0 end-0"
                                        data-bs-toggle="modal" data-bs-target="#editAddressModal">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <h4 class="section-title h5 mb-3">Adresse</h4>
                                    <div class="d-flex">
                                        <i class="fas fa-map-marker-alt text-primary mt-1 me-2"></i>
                                        <div>
                                            <p class="mb-1">{{ $etablissement->ville }}, commune :
                                                {{ $etablissement->commune }}</p>
                                            <p class="mb-2">Av. {{ $etablissement->avenue }}, N°
                                                {{ $etablissement->numero }}</p>
                                            <button class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-route me-1"></i> Itinéraire
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Gallery Section -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h4 class="section-title h5 mb-3">
                            Galerie Photo
                            <button class="btn btn-sm btn-primary add-btn" data-bs-toggle="modal"
                                data-bs-target="#addPhotoModal">
                                <i class="fas fa-plus"></i> Ajouter
                            </button>
                        </h4>

                        <div class="row g-3">
                            @forelse ($etablissement->photos as $photo)
                                <div class="col-md-4 col-lg-3">
                                    <div class="gallery-item rounded overflow-hidden">
                                        <img src="{{ asset('storage/' . str_replace('public/', '', $photo->image_path)) }}"
                                            class="img-fluid w-100" alt="{{ $photo->titre }}"
                                            style="height: 200px; object-fit: cover;"
                                            onerror="this.src='/placeholder.jpg';">

                                        <div class="gallery-actions">
                                            <button class="btn btn-sm btn-outline-light" data-bs-toggle="modal"
                                                data-bs-target="#editPhotoModal" data-photo-id="{{ $photo->id }}"
                                                data-photo-title="{{ $photo->titre }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('photos.destroy', $photo->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-light"
                                                    onclick="return confirm('Êtes-vous sûr ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center text-muted py-4">
                                    <i class="fas fa-camera fa-3x mb-3"></i>
                                    <p>Aucune photo disponible</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- =============== MODALS =============== -->

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier le profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('etablissements.updatetitre', $etablissement->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nom de l'établissement</label>
                            <input type="text" class="form-control" name="nom" value="{{ $etablissement->nom }}"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type d'établissement</label>
                            <select class="form-select" name="type_etablissement_id" required>
                                @foreach ($typesEtablissement as $type)
                                    <option value="{{ $type->id }}"
                                        {{ $etablissement->type_etablissement_id == $type->id ? 'selected' : '' }}>
                                        {{ $type->nom }}
                                    </option>
                                @endforeach
                            </select>
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

    <!-- Change Photo Modal -->
    <div class="modal fade" id="changePhotoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Changer la photo de profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('etablissements.updatePhoto', $etablissement->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nouvelle photo</label>
                            <input type="file" class="form-control" name="photo" accept="image/*" required>
                            <small class="text-muted">Format recommandé : 500x500 pixels</small>
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

    <!-- Edit Contact Modal -->
    <div class="modal fade" id="editContactModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier les contacts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('etablissements.updateContact', $etablissement->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" name="telephone"
                                value="{{ $etablissement->telephone }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email"
                                value="{{ $etablissement->email }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Site web</label>
                            <input type="url" class="form-control" name="website"
                                value="{{ $etablissement->website }}">
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

    <!-- Edit Description Modal -->
    <div class="modal fade" id="editDescriptionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier la description</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('etablissements.updateDescription', $etablissement->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="5">{{ $etablissement->description }}</textarea>
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

    <!-- Edit Address Modal -->
    <div class="modal fade" id="editAddressModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier l'adresse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('etablissements.updateAddress', $etablissement->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Ville</label>
                            <input type="text" class="form-control" name="ville"
                                value="{{ $etablissement->ville }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Commune</label>
                            <input type="text" class="form-control" name="commune"
                                value="{{ $etablissement->commune }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Avenue</label>
                            <input type="text" class="form-control" name="avenue"
                                value="{{ $etablissement->avenue }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Numéro</label>
                            <input type="text" class="form-control" name="numero"
                                value="{{ $etablissement->numero }}" required>
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

    <!-- Add Service Modal -->
    <div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('etablissements.services.store', [$etablissement->id]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="etablissement_id" value="{{ $etablissement->id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nom du service</label>
                            <input type="text" class="form-control" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prix (par jour)</label>
                            <input type="number" step="0.01" class="form-control" name="prix" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Service Modal -->
    <div class="modal fade" id="editServiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier le service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editServiceForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nom du service</label>
                            <input type="text" class="form-control" name="nom" id="serviceName" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" id="serviceDesc" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prix (par jour)</label>
                            <input type="number" step="0.01" class="form-control" name="prix" id="servicePrice"
                                required>
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

    <!-- Add Photo Modal -->
    <div class="modal fade" id="addPhotoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter des photos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('photos.stores') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="etablissement_id" value="{{ $etablissement->id }}">
                    <div class="modal-body">
                        <div class="upload-area p-4 mb-3 text-center">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <p class="mb-2">Glissez-déposez vos fichiers ici ou cliquez pour sélectionner</p>
                            <input type="file" name="photos[]" multiple accept="image/*" class="d-none"
                                id="photoUpload">
                            <button type="button" class="btn btn-outline-primary"
                                onclick="document.getElementById('photoUpload').click()">
                                Sélectionner des photos
                            </button>
                            <div id="fileNames" class="mt-3 small text-muted"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Titre des photos</label>
                            <input type="text" class="form-control" name="titre"
                                placeholder="Titre commun pour toutes les photos">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Télécharger</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Photo Modal -->
    <div class="modal fade" id="editPhotoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier la photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editPhotoForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Titre</label>
                            <input type="text" class="form-control" name="titre" id="photoTitle" required>
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

    <!-- Edit Social Media Modal -->
    <div class="modal fade" id="editSocialModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier les réseaux sociaux</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('etablissements.updateSocial', $etablissement->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Facebook</label>
                            <input type="url" class="form-control" name="facebook"
                                value="{{ $etablissement->facebook ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Twitter</label>
                            <input type="url" class="form-control" name="twitter"
                                value="{{ $etablissement->twitter ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Instagram</label>
                            <input type="url" class="form-control" name="instagram"
                                value="{{ $etablissement->instagram ?? '' }}">
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

    <script>
        // Initialize service edit modal
        document.getElementById('editServiceModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const serviceId = button.getAttribute('data-service-id');
            const serviceName = button.getAttribute('data-service-name');
            const serviceDesc = button.getAttribute('data-service-desc');
            const servicePrice = button.getAttribute('data-service-price');

            const modal = this;
            modal.querySelector('#serviceName').value = serviceName;
            modal.querySelector('#serviceDesc').value = serviceDesc;
            modal.querySelector('#servicePrice').value = servicePrice;
            modal.querySelector('#editServiceForm').action = `/services/ets/${serviceId}`;
        });

        // Initialize photo edit modal
        document.getElementById('editPhotoModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const photoId = button.getAttribute('data-photo-id');
            const photoTitle = button.getAttribute('data-photo-title');

            const modal = this;
            modal.querySelector('#photoTitle').value = photoTitle;
            modal.querySelector('#editPhotoForm').action = `/photos/${photoId}`;
        });

        // Show selected file names
        document.getElementById('photoUpload').addEventListener('change', function() {
            const fileNames = document.getElementById('fileNames');
            if (this.files.length > 0) {
                let names = '';
                for (let i = 0; i < this.files.length; i++) {
                    names += this.files[i].name + '<br>';
                }
                fileNames.innerHTML = `<strong>Fichiers sélectionnés:</strong><br>${names}`;
            } else {
                fileNames.innerHTML = '';
            }
        });

        // Drag and drop for photo upload
        const uploadArea = document.querySelector('.upload-area');
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            uploadArea.classList.add('bg-light');
        }

        function unhighlight() {
            uploadArea.classList.remove('bg-light');
        }

        uploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            document.getElementById('photoUpload').files = files;

            // Trigger change event
            const event = new Event('change');
            document.getElementById('photoUpload').dispatchEvent(event);
        }
    </script>
@endsection
