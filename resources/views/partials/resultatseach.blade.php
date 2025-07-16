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
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
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
        background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
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
        background: rgba(0, 0, 0, 0.9);
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
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .modal-nav:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .modal-prev {
        left: 1rem;
    }

    .modal-next {
        right: 1rem;
    }

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
    @php
        use Carbon\Carbon;
        $taux = \App\Models\TauxDeChange::where('date', today())->first()?->usd_cdf ?? 2500;

    @endphp
    @if (isset($photos) && $photos->count() > 0)
        <div class="ad-container position-relative">
            <h3 class="text-center mb-4 fw-semibold">Publicités</h3>

            <div class="ad-track">
                @foreach ($photos as $pub)
                    <div class="ad-slide">
                        <a href="{{ route('ets.info', [$pub->etablissement_id]) }}" class="ad-link">
                            <div class="ad-image-wrapper">
                                <img src="{{ asset('storage/' . str_replace('public/', '', $pub->image_path)) }}"
                                    class="ad-image" alt="{{ $pub->titre }}" loading="lazy"
                                    onerror="this.src='/placeholder.jpg';">
                                @if ($pub->titre)
                                    <div class="ad-title">{{ $pub->titre }}</div>
                                @endif
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <button class="carousel-nav prev" aria-label="Précédent">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="carousel-nav next" aria-label="Suivant">
                <i class="fas fa-chevron-right"></i>
            </button>

            <div class="indicators">
                @foreach ($photos as $index => $pub)
                    <button class="indicator" data-index="{{ $index }}"></button>
                @endforeach
            </div>
        </div>

        <style>
            .ad-container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 20px;
                position: relative;
            }

            .ad-track {
                display: flex;
                overflow-x: hidden;
                scroll-behavior: smooth;
                gap: 15px;
                padding: 10px 0;
            }

            .ad-slide {
                flex: 0 0 calc(33.333% - 10px);
                min-width: 300px;
                transition: transform 0.3s ease;
            }

            .ad-link {
                text-decoration: none;
                color: inherit;
            }

            .ad-image-wrapper {
                position: relative;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            .ad-image {
                width: 100%;
                height: 180px;
                object-fit: cover;
                display: block;
            }

            .ad-title {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
                color: white;
                padding: 15px;
                font-size: 1.1rem;
                margin: 0;
            }

            .carousel-nav {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: white;
                border: none;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
                cursor: pointer;
                z-index: 10;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .carousel-nav.prev {
                left: 10px;
            }

            .carousel-nav.next {
                right: 10px;
            }

            .carousel-nav:hover {
                background: #f8f9fa;
            }

            .indicators {
                display: flex;
                justify-content: center;
                gap: 8px;
                margin-top: 15px;
            }

            .indicator {
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background: #ddd;
                border: none;
                padding: 0;
                cursor: pointer;
            }

            .indicator.active {
                background: #333;
            }

            @media (max-width: 992px) {
                .ad-slide {
                    flex: 0 0 calc(50% - 10px);
                }
            }

            @media (max-width: 576px) {
                .ad-slide {
                    flex: 0 0 100%;
                }

                .carousel-nav {
                    width: 30px;
                    height: 30px;
                }
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const track = document.querySelector('.ad-track');
                const slides = document.querySelectorAll('.ad-slide');
                const prevBtn = document.querySelector('.prev');
                const nextBtn = document.querySelector('.next');
                const indicators = document.querySelectorAll('.indicator');
                let currentIndex = 0;
                const slideCount = slides.length;

                // Initialisation
                updateCarousel();

                // Navigation
                prevBtn.addEventListener('click', () => {
                    if (currentIndex > 0) {
                        currentIndex--;
                        updateCarousel();
                    }
                });

                nextBtn.addEventListener('click', () => {
                    if (currentIndex < slideCount - 1) {
                        currentIndex++;
                        updateCarousel();
                    }
                });

                // Indicateurs
                indicators.forEach((indicator, index) => {
                    indicator.addEventListener('click', () => {
                        currentIndex = index;
                        updateCarousel();
                    });
                });

                function updateCarousel() {
                    const slideWidth = slides[0].offsetWidth;
                    track.scrollTo({
                        left: slideWidth * currentIndex,
                        behavior: 'smooth'
                    });

                    // Mettre à jour les indicateurs
                    indicators.forEach((indicator, index) => {
                        indicator.classList.toggle('active', index === currentIndex);
                    });

                    // Masquer/afficher les boutons de navigation
                    prevBtn.style.display = currentIndex === 0 ? 'none' : 'flex';
                    nextBtn.style.display = currentIndex >= slideCount - 1 ? 'none' : 'flex';
                }

                // Redimensionnement de la fenêtre
                window.addEventListener('resize', updateCarousel);
            });
        </script>
    @endif


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

                            <h4 class="section-title h5 mb-3">Services</h4>
                            <div class="table-responsive mb-4">
                                <div class="mb-3">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                            id="btn-usd">Afficher en USD</button>
                                        <button type="button" class="btn btn-outline-success btn-sm active"
                                            id="btn-cdf">Afficher en CDF</button>
                                    </div>
                                </div>


                                <table class="table price-table table-hover table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col">Type</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Tarifs</th>
                                            <th scope="col">Disponibilites</th>
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

                                                @php
                                                    $prixInitial = $service->prix;
                                                    $promotion = $service->promotion ?? 0;
                                                    $dateFinPromo = $service->date_fin_promo ?? null;
                                                    $promoValide =
                                                        $promotion > 0 &&
                                                        (!$dateFinPromo || Carbon::parse($dateFinPromo)->isFuture());

                                                    $reductionUSD = $promoValide
                                                        ? ($prixInitial * $promotion) / 100
                                                        : 0;
                                                    $prixUSD = $prixInitial - $reductionUSD;

                                                    $prixCDFInitial = $prixInitial * $taux;
                                                    $reductionCDF = $promoValide
                                                        ? ($prixCDFInitial * $promotion) / 100
                                                        : 0;
                                                    $prixCDF = $prixCDFInitial - $reductionCDF;
                                                @endphp

                                                <td>
                                                    <div class="usd-price d-none">
                                                        @if ($promoValide)
                                                            <span class="badge bg-danger mb-1">Promo
                                                                -{{ $promotion }}%</span><br>
                                                        @endif
                                                        <strong>{{ number_format($prixUSD, 2) }} $</strong>
                                                    </div>

                                                    <div class="cdf-price">
                                                        @if ($promoValide)
                                                            <span class="badge bg-danger mb-1">Promo
                                                                -{{ $promotion }}%</span><br>
                                                        @endif
                                                        <strong>{{ number_format($prixCDF, 0) }} CDF</strong>
                                                    </div>
                                                </td>



                                                <td>
                                                    @php
                                                        // Préparation des dates déjà réservées (version corrigée)
                                                        $datesConfirmees = $service->reservations
                                                            ->where('statut', 'confirmé')
                                                            ->pluck('date')
                                                            ->map(function ($date) {
                                                                // Si c'est déjà une string, la retourner directement
        if (is_string($date)) {
            return $date;
        }
        // Si c'est un objet DateTime/Carbon, le formater
                                                                return $date->format('Y-m-d');
                                                            })
                                                            ->toArray();
                                                    @endphp

                                                    <button class="btn btn-sm btn-outline-primary mb-2"
                                                        onclick="toggleCalendar({{ $service->id }})">
                                                        Voir les dates
                                                    </button>

                                                    <div id="calendar-wrapper-{{ $service->id }}" class="d-none">
                                                        <input type="text" id="calendar-{{ $service->id }}"
                                                            class="calendar" readonly />
                                                        <button class="btn btn-success btn-sm mt-2"
                                                            onclick="openReservationModal({{ $service->id }})">Réserver</button>
                                                    </div>

                                                    <!-- Modale de réservation -->
                                                    <div class="modal fade" id="reservationModal-{{ $service->id }}"
                                                        tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form
                                                                    onsubmit="submitReservation(event, {{ $service->id }})">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Réservation -
                                                                            {{ $service->nom }}</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <input type="hidden"
                                                                            id="selected-date-{{ $service->id }}">
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Nom complet</label>
                                                                            <input type="text" class="form-control"
                                                                                id="client-name-{{ $service->id }}"
                                                                                required>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label">Téléphone</label>
                                                                            <input type="tel" class="form-control"
                                                                                id="client-phone-{{ $service->id }}"
                                                                                required>
                                                                        </div>

                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-bs-dismiss="modal">Annuler</button>
                                                                        <button type="submit"
                                                                            class="btn btn-primary">Confirmer la
                                                                            réservation</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <script>
                                                        // Initialisation du tableau des dates sélectionnées
                                                        window.selectedDates = window.selectedDates || {};

                                                        // Configuration du calendrier
                                                        flatpickr("#calendar-{{ $service->id }}", {
                                                            inline: true,
                                                            locale: "fr",
                                                            minDate: "today",
                                                            dateFormat: "Y-m-d",
                                                            altInput: true,
                                                            altFormat: "j F Y",
                                                            disable: @json($datesConfirmees),
                                                            onChange: function(selectedDates) {
                                                                if (selectedDates.length > 0) {
                                                                    window.selectedDates[{{ $service->id }}] = selectedDates[0];
                                                                    document.getElementById("selected-date-{{ $service->id }}").value =
                                                                        selectedDates[0].toISOString().split('T')[0];
                                                                }
                                                            }
                                                        });

                                                        // Fonction pour afficher/masquer le calendrier
                                                        function toggleCalendar(id) {
                                                            const calendarWrapper = document.getElementById('calendar-wrapper-' + id);
                                                            calendarWrapper.classList.toggle('d-none');

                                                            // Fermer les autres calendriers ouverts
                                                            document.querySelectorAll('.calendar-wrapper').forEach(wrapper => {
                                                                if (wrapper.id !== 'calendar-wrapper-' + id && !wrapper.classList.contains('d-none')) {
                                                                    wrapper.classList.add('d-none');
                                                                }
                                                            });
                                                        }

                                                        // Fonction pour ouvrir la modale de réservation
                                                        function openReservationModal(serviceId) {
                                                            const date = window.selectedDates[serviceId];
                                                            if (!date) {
                                                                return alert("Veuillez sélectionner une date valide.");
                                                            }

                                                            const modal = new bootstrap.Modal(document.getElementById('reservationModal-' + serviceId));
                                                            modal.show();
                                                        }

                                                        // Fonction pour soumettre la réservation
                                                        function submitReservation(event, serviceId) {
                                                            event.preventDefault();

                                                            const date = document.getElementById('selected-date-' + serviceId).value;
                                                            const name = document.getElementById('client-name-' + serviceId).value.trim();
                                                            const phone = document.getElementById('client-phone-' + serviceId).value.trim();


                                                            if (!date || !name || !phone) {
                                                                return alert("Veuillez remplir tous les champs obligatoires.");
                                                            }

                                                            fetch('/reservations', {
                                                                    method: 'POST',
                                                                    headers: {
                                                                        'Content-Type': 'application/json',
                                                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                        'Accept': 'application/json'
                                                                    },
                                                                    body: JSON.stringify({
                                                                        service_id: serviceId,
                                                                        client_name: name,
                                                                        client_phone: phone,

                                                                        date: date
                                                                    })
                                                                })
                                                                .then(response => {
                                                                    if (!response.ok) {
                                                                        throw new Error('Erreur réseau');
                                                                    }
                                                                    return response.json();
                                                                })
                                                                .then(data => {
                                                                    if (data.success) {
                                                                        alert("Réservation confirmée avec succès !");
                                                                        const modal = bootstrap.Modal.getInstance(document.getElementById('reservationModal-' +
                                                                            serviceId));
                                                                        modal.hide();

                                                                        // Recharger la page ou mettre à jour l'interface
                                                                        window.location.reload();
                                                                    } else {
                                                                        alert(data.message || "Erreur lors de la réservation");
                                                                    }
                                                                })
                                                                .catch(error => {
                                                                    console.error('Error:', error);
                                                                    alert("Une erreur est survenue lors de la réservation.");
                                                                });
                                                        }
                                                    </script>
                                                </td>

                                                <style>
                                                    /* Style personnalisé pour le calendrier */
                                                    .flatpickr-calendar.inline {
                                                        max-width: 280px;
                                                        font-size: 12px;
                                                        padding: 5px;
                                                        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                                                        border-radius: 8px;
                                                    }

                                                    .flatpickr-day {
                                                        width: 30px;
                                                        height: 25px;
                                                        line-height: 25px;
                                                        margin: 1px;
                                                        font-size: 12px;
                                                    }

                                                    .flatpickr-months,
                                                    .flatpickr-weekdays {
                                                        font-size: 12px;
                                                    }

                                                    .flatpickr-month {
                                                        padding: 5px 0;
                                                    }

                                                    .flatpickr-weekday {
                                                        font-weight: 500;
                                                    }

                                                    .flatpickr-day.selected,
                                                    .flatpickr-day.selected:hover {
                                                        background: #0d6efd;
                                                        border-color: #0d6efd;
                                                    }
                                                </style>


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
                                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#comingSoonModal">
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
                                    alt="{{ $photo->titre }}" loading="lazy" onerror="this.src='/placeholder.jpg';">
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
    <div class="modal fade" id="comingSoonModal" tabindex="-1" aria-labelledby="comingSoonModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="comingSoonModalLabel">Fonctionnalité en développement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    Cette fonctionnalité n'est pas encore disponible. Revenez bientôt !
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>



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
    <script>
        const btnUSD = document.getElementById('btn-usd');
        const btnCDF = document.getElementById('btn-cdf');

        btnUSD.addEventListener('click', () => {
            btnUSD.classList.add('active');
            btnCDF.classList.remove('active');
            document.querySelectorAll('.usd-price').forEach(el => el.classList.remove('d-none'));
            document.querySelectorAll('.cdf-price').forEach(el => el.classList.add('d-none'));
        });

        btnCDF.addEventListener('click', () => {
            btnCDF.classList.add('active');
            btnUSD.classList.remove('active');
            document.querySelectorAll('.usd-price').forEach(el => el.classList.add('d-none'));
            document.querySelectorAll('.cdf-price').forEach(el => el.classList.remove('d-none'));
        });
    </script>

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

            switch (e.key) {
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
