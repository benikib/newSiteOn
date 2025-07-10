@extends('layouts.app')

@section('styles')
    <style>
        /* Styles généraux */
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #3b82f6;
            --text-color: #1f2937;
            --light-bg: #f3f4f6;
        }

        body {
            color: var(--text-color);
            background-color: #f8fafc;
        }

        /* Carrousel amélioré */
        .ad-carousel-container {
            position: relative;
            padding: 2rem 0;
            background: linear-gradient(to right, #ffffff, var(--light-bg), #ffffff);
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            margin: 2rem 0;
            overflow: hidden;
        }

        .ad-track {
            display: flex;
            gap: 1.5rem;
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 1rem;
            position: relative;
            left: 50%;
            transform: translateX(-50%);
            width: fit-content;
        }

        .ad-slide {
            flex: 0 0 calc(25% - 1.5rem);
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: white;
        }

        .ad-slide:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }

        .ad-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 0.75rem 0.75rem 0 0;
        }

        .ad-title {
            padding: 1rem;
            font-weight: 600;
            color: var(--text-color);
            background: white;
            text-align: center;
        }

        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgb(0 0 0 / 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .carousel-nav:hover {
            background: var(--primary-color);
            color: white;
        }

        .carousel-nav.prev {
            left: 1rem;
        }

        .carousel-nav.next {
            right: 1rem;
        }

        /* Section Recherche améliorée */
        .search-container {
            background: white;
            padding: 3rem 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            margin: 2rem 0;
            transition: transform 0.3s ease;
        }

        .search-container:hover {
            transform: translateY(-2px);
        }

        .search-container h2 {
            color: var(--text-color);
            font-size: 2rem;
            margin-bottom: 1.5rem;
            position: relative;
            display: inline-block;
        }

        .search-container h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: var(--primary-color);
            border-radius: 2px;
        }

        .form-control {
            border: 2px solid #e5e7eb;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-1px);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-1px);
        }

        /* Recherche Avancée améliorée */
        .advanced-search-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            transition: all 0.3s ease;
        }

        .advanced-search-card:hover {
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }

        .card-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border-radius: 1rem 1rem 0 0 !important;
            padding: 1.5rem;
        }

        .card-header h5 {
            font-size: 1.25rem;
            margin: 0;
        }

        .card-body {
            padding: 2rem;
        }

        .form-label {
            color: var(--text-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-select {
            border: 2px solid #e5e7eb;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .search-container,
        .advanced-search-card {
            animation: fadeIn 0.5s ease-out;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .ad-slide {
                flex: 0 0 calc(50% - 1rem);
            }

            .search-container {
                padding: 2rem 1rem;
            }

            .card-body {
                padding: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .ad-slide {
                flex: 0 0 100%;
            }

            .search-container h2 {
                font-size: 1.5rem;
            }
        }

        /* Ajout des styles d'animation pour le carrousel */
        .ad-track.sliding {
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .ad-track.sliding-left {
            animation: slideLeft 0.5s ease-out;
        }

        .ad-track.sliding-right {
            animation: slideRight 0.5s ease-out;
        }

        @keyframes slideLeft {
            from {
                transform: translateX(calc(-50% + 20px));
            }

            to {
                transform: translateX(-50%);
            }
        }

        @keyframes slideRight {
            from {
                transform: translateX(calc(-50% - 20px));
            }

            to {
                transform: translateX(-50%);
            }
        }

        /* Amélioration des indicateurs */
        .indicators {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #e5e7eb;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .indicator.active {
            background-color: var(--primary-color);
            transform: scale(1.2);
        }

        .indicator:hover {
            background-color: var(--accent-color);
        }
    </style>
@endsection

@section('content')
    <!-- Carrousel Publicitaire -->
    <div class="container mt-4">
        <div class="ad-carousel-container">


            @if (isset($photos) && $photos->isNotEmpty())
            <h3 class="text-center mb-4 fw-semibold">Publicités</h3>
                <div class="ad-track">
                    @foreach ($photos as $pub)
                        <a href="{{ route('ets.info', [$pub->etablissement_id]) }}" class="ad-slide">
                            <img src="{{ asset('storage/' . str_replace('public/', '', $pub->image_path)) }}" class="ad-image"
                                alt="{{ $pub->titre }}" loading="lazy" onerror="this.src='/placeholder.jpg';">
                            @if ($pub->titre)
                                <div class="ad-title">{{ $pub->titre }}</div>
                            @endif
                        </a>
                    @endforeach
                </div>

                <button class="carousel-nav prev" aria-label="Précédent">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="carousel-nav next" aria-label="Suivant">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="indicators"></div>
            @else

            @endif
        </div>
    </div>

    <!-- Section Recherche -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="search-container text-center">
                    <h2 class="mb-4 fw-bold">Trouvez ce que vous cherchez</h2>
                    <form action="{{ route('search') }}" method="GET">
                        <div class="input-group mb-3">
                            <input type="text" name="query" class="form-control form-control-lg border-primary"
                                placeholder="Rechercher un établissement, service..." value="{{ request('query') }}">
                            <button class="btn btn-primary px-4" type="submit">
                                <i class="fas fa-search me-2"></i>Rechercher
                            </button>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-advanced mt-2">
                            <i class="fas fa-sliders-h me-2"></i>Recherche avancée
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Recherche Avancée -->
    <div class="container mt-4" id="advancedSearch" style="display: none;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card advanced-search-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-search-plus me-2"></i>Recherche Avancée</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('search') }}" method="GET">
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Mots-clés</label>
                                    <input type="text" name="query" class="form-control border-primary"
                                        value="{{ request('query') }}" placeholder="Nom, description, service...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Type d'établissement</label>
                                    <select class="form-select border-primary" name="type_etablissement">
                                        <option value="">Tous types</option>
                                        @foreach ($typesAvecEtablissements as $type)
                                            <option value="{{ $type->nom }}"
                                                {{ request('type_etablissement') == $type->nom ? 'selected' : '' }}>
                                                {{ $type->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Ville</label>
                                    <input type="text" name="ville" class="form-control border-primary"
                                        value="{{ request('ville') }}" placeholder="Kinshasa, Goma...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Commune</label>
                                    <input type="text" name="commune" class="form-control border-primary"
                                        value="{{ request('commune') }}" placeholder="Gombe, Kalamu...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Quartier</label>
                                    <input type="text" name="quartier" class="form-control border-primary"
                                        value="{{ request('quartier') }}" placeholder="Yolo, Matonge...">
                                </div>
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Note minimale</label>
                                    <select class="form-select border-primary" name="note_min">
                                        <option value="">Toutes notes</option>
                                        <option value="3" {{ request('note_min') == '3' ? 'selected' : '' }}>3+
                                        </option>
                                        <option value="4" {{ request('note_min') == '4' ? 'selected' : '' }}>4+
                                        </option>
                                        <option value="5" {{ request('note_min') == '5' ? 'selected' : '' }}>5
                                            étoiles</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Budget (max)</label>
                                    <div class="input-group">
                                        <input type="number" name="budget_max" class="form-control border-primary"
                                            value="{{ request('budget_max') }}" placeholder="Montant maximum">
                                        <span class="input-group-text">$</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check mt-4 pt-2">
                                        <input class="form-check-input" type="checkbox" name="has_promotion"
                                            id="hasPromotion" value="1"
                                            {{ request('has_promotion') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="hasPromotion">
                                            Avec promotions actives
                                        </label>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <div class="form-check mt-4 pt-2">
                                        <input class="form-check-input" type="checkbox" name="has_publicite"
                                            id="hasPublicite" value="1" {{ request('has_publicite') ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="hasPublicite">
                                            Avec publicités actives
                                        </label>
                                    </div>
                                </div>
                            </div> --}}
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary px-4 me-3">
                                    <i class="fas fa-search me-2"></i>Appliquer
                                </button>
                                <button type="button" id="hideAdvanced" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Fermer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS + Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Carrousel Publicitaire
            const track = document.querySelector('.ad-track');
            const slides = document.querySelectorAll('.ad-slide');
            const prevBtn = document.querySelector('.carousel-nav.prev');
            const nextBtn = document.querySelector('.carousel-nav.next');
            const indicatorsContainer = document.querySelector('.indicators');

            if (track && slides.length > 0) {
                const slideCount = slides.length;
                const visibleSlides = 4;
                let currentIndex = Math.floor(slideCount / 2); // Commencer au milieu
                const slideWidth = slides[0].offsetWidth + 24; // inclut le gap (1.5rem = 24px)

                // Initialiser la position au milieu
                function initializeCarousel() {
                    const centerOffset = -(currentIndex * slideWidth) + (track.offsetWidth / 2) - (slideWidth / 2);
                    track.style.transform = `translateX(${centerOffset}px)`;
                    updateIndicators();
                }

                // Créer les indicateurs
                for (let i = 0; i < slideCount; i++) {
                    const indicator = document.createElement('div');
                    indicator.classList.add('indicator');
                    if (i === currentIndex) indicator.classList.add('active');
                    indicator.addEventListener('click', () => goToSlide(i));
                    indicatorsContainer.appendChild(indicator);
                }

                // Navigation
                prevBtn.addEventListener('click', () => {
                    if (currentIndex > 0) {
                        currentIndex--;
                        updateCarousel('prev');
                    }
                });

                nextBtn.addEventListener('click', () => {
                    if (currentIndex < slideCount - 1) {
                        currentIndex++;
                        updateCarousel('next');
                    }
                });

                function updateCarousel(direction) {
                    const centerOffset = -(currentIndex * slideWidth) + (track.offsetWidth / 2) - (slideWidth / 2);

                    // Ajouter une classe pour l'animation
                    track.classList.add('sliding');
                    if (direction === 'prev') {
                        track.classList.add('sliding-left');
                    } else if (direction === 'next') {
                        track.classList.add('sliding-right');
                    }

                    track.style.transform = `translateX(${centerOffset}px)`;
                    updateIndicators();

                    // Retirer les classes d'animation après la transition
                    setTimeout(() => {
                        track.classList.remove('sliding', 'sliding-left', 'sliding-right');
                    }, 500);
                }

                function updateIndicators() {
                    document.querySelectorAll('.indicator').forEach((ind, index) => {
                        ind.classList.toggle('active', index === currentIndex);
                    });
                }

                function goToSlide(index) {
                    if (index === currentIndex) return;

                    const direction = index > currentIndex ? 'next' : 'prev';
                    currentIndex = index;
                    updateCarousel(direction);
                }

                // Auto-rotation avec pause au survol
                let autoSlide = setInterval(() => {
                    if (currentIndex < slideCount - 1) {
                        currentIndex++;
                        updateCarousel('next');
                    } else {
                        currentIndex = 0;
                        updateCarousel('next');
                    }
                }, 5000);

                track.addEventListener('mouseenter', () => clearInterval(autoSlide));
                track.addEventListener('mouseleave', () => {
                    autoSlide = setInterval(() => {
                        if (currentIndex < slideCount - 1) {
                            currentIndex++;
                            updateCarousel('next');
                        } else {
                            currentIndex = 0;
                            updateCarousel('next');
                        }
                    }, 5000);
                });

                // Initialiser le carrousel
                initializeCarousel();

                // Redimensionnement
                let resizeTimer;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(() => {
                        const newSlideWidth = slides[0].offsetWidth + 24;
                        const centerOffset = -(currentIndex * newSlideWidth) + (track.offsetWidth /
                            2) - (newSlideWidth / 2);
                        track.style.transform = `translateX(${centerOffset}px)`;
                    }, 250);
                });
            }

            // Recherche Avancée
            const advancedBtn = document.querySelector('.btn-advanced');
            const advancedSearch = document.getElementById('advancedSearch');
            const hideAdvanced = document.getElementById('hideAdvanced');

            if (advancedBtn && advancedSearch && hideAdvanced) {
                advancedBtn.addEventListener('click', function() {
                    advancedSearch.style.display = 'block';
                    advancedSearch.style.opacity = '0';
                    advancedSearch.style.transform = 'translateY(20px)';

                    setTimeout(() => {
                        advancedSearch.style.transition = 'all 0.3s ease-out';
                        advancedSearch.style.opacity = '1';
                        advancedSearch.style.transform = 'translateY(0)';
                    }, 10);

                    window.scrollTo({
                        top: advancedSearch.offsetTop - 30,
                        behavior: 'smooth'
                    });
                });

                hideAdvanced.addEventListener('click', function() {
                    advancedSearch.style.opacity = '0';
                    advancedSearch.style.transform = 'translateY(20px)';

                    setTimeout(() => {
                        advancedSearch.style.display = 'none';
                    }, 300);
                });
            }

            // Animation des inputs au focus
            const inputs = document.querySelectorAll('.form-control, .form-select');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                    this.parentElement.style.transition = 'transform 0.3s ease';
                });

                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
@endsection
