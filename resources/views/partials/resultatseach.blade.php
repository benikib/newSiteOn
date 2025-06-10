@extends('layouts.app')
<style>
    /* Styles personnalisés complémentaires à Bootstrap */
    .profile-card {
        border-radius: 1rem;
        overflow: hidden;
    }

    .profile-img {
        width: 160px;
        height: 160px;
        object-fit: cover;
        border: 5px solid white;
    }

    .section-title {
        border-left: 4px solid #0d6efd;
        padding-left: 0.75rem;
    }

    .contact-box {
        background-color: rgba(13, 110, 253, 0.05);
        border: 1px solid rgba(13, 110, 253, 0.1);
    }

    .promo-badge {
        top: 1rem;
        right: 1rem;
    }

    .description-text {
        text-align: justify;
    }

    .price-table thead {
        background-color: rgba(13, 110, 253, 0.1);
    }

    .price-table tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.03);
    }

    /* Styles pour la galerie photo */
    .gallery-section {
        margin-top: 2rem;
        padding: 2rem 0;
        background-color: #f8f9fa;
        border-radius: 1rem;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
        padding: 1rem;
    }

    .gallery-item {
        position: relative;
        border-radius: 0.5rem;
        overflow: hidden;
        aspect-ratio: 1;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .gallery-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .gallery-item:hover img {
        transform: scale(1.05);
    }

    .gallery-item .overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
        padding: 1rem;
        color: white;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .gallery-item:hover .overlay {
        opacity: 1;
    }

    /* Modal de la galerie */
    .gallery-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.9);
        z-index: 1000;
        padding: 2rem;
    }

    .gallery-modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        max-width: 90%;
        max-height: 90vh;
        position: relative;
    }

    .modal-content img {
        max-width: 100%;
        max-height: 90vh;
        object-fit: contain;
    }

    .modal-close {
        position: absolute;
        top: -2rem;
        right: 0;
        color: white;
        font-size: 2rem;
        cursor: pointer;
        background: none;
        border: none;
        padding: 0.5rem;
    }

    .modal-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255,255,255,0.1);
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .modal-nav:hover {
        background: rgba(255,255,255,0.2);
    }

    .modal-prev { left: 1rem; }
    .modal-next { right: 1rem; }

    .gallery-title {
        position: relative;
        margin-bottom: 2rem;
        padding-bottom: 0.5rem;
    }

    .gallery-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background: var(--primary-color, #0d6efd);
    }
</style>
@section('content')
    <div class="container py-4 py-lg-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="profile-card bg-white shadow-sm p-3 p-md-4">
                    <div class="row g-4">
                        <!-- Colonne gauche (photo + contact) -->
                        <div class="col-md-4 text-center text-md-start">
                            <div class="position-relative mb-4">
                                @if ($etablissement->photos->isNotEmpty())
                                    <img src="{{ asset('storage/' . str_replace('public/', '', $etablissement->photos->first()->image_path)) }}"
                                        class="profile-img rounded-circle shadow"
                                        alt="{{ $etablissement->photos->first()->titre }}"
                                        onerror="this.src='/placeholder.jpg';">
                                    {{-- <span class="promo-badge position-absolute badge bg-warning text-dark">Promo</span> --}}
                                @else
                                    <img src="/placeholder.jpg" class="profile-img rounded-circle shadow"
                                        alt="Photo par défaut" />
                                @endif
                            </div>

                            <div class="contact-box rounded p-3 p-md-4 mb-3">
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

                            <!-- Ajout de réseaux sociaux (optionnel) -->
                            <div class="d-flex justify-content-center gap-3">
                                <a href="#" class="text-primary"><i class="fab fa-facebook-f fa-lg"></i></a>
                                <a href="#" class="text-primary"><i class="fab fa-twitter fa-lg"></i></a>
                                <a href="#" class="text-primary"><i class="fab fa-instagram fa-lg"></i></a>
                            </div>
                        </div>

                        <!-- Colonne droite (infos + services + adresse) -->
                        <div class="col-md-8">
                            <h1 class="h2 mb-3">{{ $etablissement->nom }}</h1>

                            <!-- Note moyenne (optionnel) -->
                            {{-- <div class="d-flex align-items-center mb-3">
                                <div class="text-warning me-2">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                                <span class="text-muted">(24 avis)</span>
                            </div> --}}

                            <p class="description-text text-muted mb-4">{{ $etablissement->description }}</p>

                            <h4 class="section-title h5 mb-3">Tarifs</h4>
                            <div class="table-responsive mb-4">
                                <table class="table price-table table-hover table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Type</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Prix/jour</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($etablissement->services as $service)
                                            <tr>
                                                <td>{{ $service->nom }}</td>
                                                <td>
                                                    <p class="text-muted mb-0 text-truncate" style="max-width: 300px;"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ $service->description ?? 'Aucune description disponible' }}">
                                                        {{ $service->description ? Str::limit($service->description, 50, '...') : 'Aucune description' }}
                                                    </p>
                                                </td>
                                                <td class="fw-bold">{{ number_format($service->prix, 2) }} $</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-3">Aucun service
                                                    disponible</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
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

                                {{-- <div class="col-md-6">
                                    <h4 class="section-title h5 mb-3">Horaires</h4>
                                    <ul class="list-unstyled">
                                        <li class="d-flex justify-content-between mb-1">
                                            <span>Lundi - Vendredi</span>
                                            <span class="fw-bold">08:00 - 18:00</span>
                                        </li>
                                        <li class="d-flex justify-content-between mb-1">
                                            <span>Samedi</span>
                                            <span class="fw-bold">09:00 - 14:00</span>
                                        </li>
                                        <li class="d-flex justify-content-between">
                                            <span>Dimanche</span>
                                            <span class="fw-bold">Fermé</span>
                                        </li>
                                    </ul>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Galerie Photo -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="gallery-section">
                    <h3 class="gallery-title">Galerie Photo</h3>
                    <div class="gallery-grid">
                        @forelse ($etablissement->photos as $photo)
                            <div class="gallery-item" onclick="openGallery({{ $loop->index }})">
                                <img src="{{ asset('storage/' . str_replace('public/', '', $photo->image_path)) }}"
                                     alt="{{ $photo->titre }}"
                                     loading="lazy"
                                     onerror="this.src='/placeholder.jpg';">
                                <div class="overlay">
                                    <p class="mb-0">{{ $photo->titre }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted">
                                <p>Aucune photo disponible</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Galerie -->
    <div class="gallery-modal" id="galleryModal">
        <button class="modal-close" onclick="closeGallery()">&times;</button>
        <button class="modal-nav modal-prev" onclick="prevImage()">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="modal-nav modal-next" onclick="nextImage()">
            <i class="fas fa-chevron-right"></i>
        </button>
        <div class="modal-content">
            <img id="modalImage" src="" alt="">
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Activation des tooltips Bootstrap
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Animation pour le badge promo
            const promoBadge = document.querySelector('.promo-badge');
            if (promoBadge) {
                promoBadge.addEventListener('mouseenter', function() {
                    this.classList.add('animate__animated', 'animate__pulse');
                });
                promoBadge.addEventListener('mouseleave', function() {
                    this.classList.remove('animate__animated', 'animate__pulse');
                });
            }
        });

        // Script pour la galerie
        let currentImageIndex = 0;
        const photos = @json($etablissement->photos);
        const modal = document.getElementById('galleryModal');
        const modalImage = document.getElementById('modalImage');

        function openGallery(index) {
            currentImageIndex = index;
            updateModalImage();
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeGallery() {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        function updateModalImage() {
            if (photos[currentImageIndex]) {
                const imagePath = photos[currentImageIndex].image_path.replace('public/', '');
                modalImage.src = `/storage/${imagePath}`;
                modalImage.alt = photos[currentImageIndex].titre;
            }
        }

        function prevImage() {
            currentImageIndex = (currentImageIndex - 1 + photos.length) % photos.length;
            updateModalImage();
        }

        function nextImage() {
            currentImageIndex = (currentImageIndex + 1) % photos.length;
            updateModalImage();
        }

        // Navigation au clavier
        document.addEventListener('keydown', function(e) {
            if (!modal.classList.contains('active')) return;

            switch(e.key) {
                case 'Escape':
                    closeGallery();
                    break;
                case 'ArrowLeft':
                    prevImage();
                    break;
                case 'ArrowRight':
                    nextImage();
                    break;
            }
        });

        // Fermer la modal en cliquant en dehors de l'image
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeGallery();
            }
        });
    </script>
@endsection
