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
    </script>
@endsection
