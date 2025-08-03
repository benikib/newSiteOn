@extends('layouts.app')

@section('styles')
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --accent-color: #4895ef;
            --dark-color: #1a1a2e;
            --light-color: #f8f9fa;
            --gradient-primary: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }

        body {
            background-color: #f8fafc;
            font-family: 'Poppins', sans-serif;
            color: #2d3748;
        }

        /* Carrousel amélioré avec effet glassmorphism */
        .ad-carousel-container {
            position: relative;
            padding: 3rem 0;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
            margin: 3rem auto;
            max-width: 95%;
            border: 1px solid rgba(255, 255, 255, 0.18);
            overflow: hidden;
        }

        .ad-carousel-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 120px;
            background: var(--gradient-primary);
            z-index: -1;
            border-radius: 20px 20px 0 0;
        }

        .ad-track {
            display: flex;
            gap: 2rem;
            transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            padding: 1.5rem;
        }

        .ad-slide {
            flex: 0 0 calc(25% - 2rem);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: white;
            position: relative;
        }

        .ad-slide::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .ad-slide:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
        }

        .ad-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .ad-slide:hover .ad-image {
            transform: scale(1.05);
        }

        .ad-title {
            padding: 1.25rem;
            font-weight: 600;
            color: var(--dark-color);
            background: white;
            text-align: center;
            font-size: 1.1rem;
        }

        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: white;
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
        }

        .carousel-nav:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-50%) scale(1.1);
        }

        .carousel-nav.prev {
            left: 1.5rem;
        }

        .carousel-nav.next {
            right: 1.5rem;
        }

        /* Section Recherche avec effet néomorphisme */
        .search-container {
            background: white;
            padding: 3.5rem 2.5rem;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.05);
            margin: 3rem auto;
            max-width: 900px;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }

        .search-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: var(--gradient-primary);
        }

        .search-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .search-container h2 {
            color: var(--dark-color);
            font-size: 2.2rem;
            margin-bottom: 1.5rem;
            position: relative;
            display: inline-block;
            font-weight: 700;
        }

        .search-container h2::after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--gradient-primary);
            border-radius: 4px;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            padding: 0.9rem 1.25rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 1.05rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.15);
        }

        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            padding: 0.9rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(67, 97, 238, 0.3);
        }

        .btn-primary:hover::after {
            opacity: 1;
        }

        .btn-primary span {
            position: relative;
            z-index: 1;
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.8rem 1.8rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(67, 97, 238, 0.2);
        }

        /* Recherche Avancée améliorée */
        .advanced-search-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            overflow: hidden;
        }

        .advanced-search-card:hover {
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.12);
            transform: translateY(-5px);
        }

        .card-header {
            background: var(--gradient-primary);
            border-radius: 20px 20px 0 0 !important;
            padding: 1.75rem;
        }

        .card-header h5 {
            font-size: 1.5rem;
            margin: 0;
            font-weight: 700;
            color: white;
        }

        .card-body {
            padding: 2.5rem;
        }

        .form-label {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 0.75rem;
            font-size: 1.05rem;
        }

        .form-select {
            border: 2px solid #e2e8f0;
            padding: 0.9rem 1.25rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 1.05rem;
        }

        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.15);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .search-container,
        .advanced-search-card {
            animation: fadeInUp 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* Indicateurs du carrousel */
        .indicators {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #e2e8f0;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .indicator.active {
            background: var(--gradient-primary);
            transform: scale(1.3);
            box-shadow: 0 2px 5px rgba(67, 97, 238, 0.3);
        }

        .indicator:hover {
            background-color: var(--accent-color);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .ad-slide {
                flex: 0 0 calc(33.333% - 2rem);
            }
        }

        @media (max-width: 992px) {
            .ad-slide {
                flex: 0 0 calc(50% - 2rem);
            }

            .search-container {
                padding: 2.5rem 1.5rem;
            }

            .card-body {
                padding: 2rem;
            }
        }

        @media (max-width: 768px) {
            .ad-carousel-container {
                padding: 2rem 0;
            }

            .ad-slide {
                flex: 0 0 calc(100% - 2rem);
            }

            .search-container h2 {
                font-size: 1.8rem;
            }

            .card-body {
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .search-container {
                padding: 2rem 1rem;
            }

            .search-container h2 {
                font-size: 1.5rem;
            }

            .form-control,
            .form-select {
                padding: 0.75rem 1rem;
            }

            .btn-primary,
            .btn-outline-primary {
                padding: 0.75rem 1.5rem;
            }
        }

        /* Effet de vague décoratif */
        .wave-decoration {
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 100px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%234361ee' fill-opacity='0.1' d='M0,192L48,197.3C96,203,192,213,288,229.3C384,245,480,267,576,250.7C672,235,768,181,864,181.3C960,181,1056,235,1152,234.7C1248,235,1344,181,1392,154.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E");
            background-size: cover;
            background-repeat: no-repeat;
            z-index: -1;
        }
    </style>
@endsection

@section('content')
    <!-- Carrousel Publicitaire -->
    <div class="container mt-5">
        <div class="ad-carousel-container position-relative">
            @if (isset($photos) && $photos->isNotEmpty())
                <h3 class="text-center mb-5 fw-bold" style="color: white;">Découvrez nos établissements</h3>
                <div class="ad-track">
                    @foreach ($photos as $pub)
                        <a href="{{ route('ets.info', [$pub->etablissement_id]) }}" class="ad-slide">
                            <img src="{{ asset('storage/' . str_replace('public/', '', $pub->image_path)) }}"
                                class="ad-image" alt="{{ $pub->titre }}" loading="lazy"
                                onerror="this.src='/placeholder.jpg';">
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
                <div class="text-center py-4">
                    <p class="text-muted">Aucune publicité disponible pour le moment</p>
                </div>
            @endif
            <div class="wave-decoration"></div>
        </div>
    </div>

    <!-- Section Recherche -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="search-container text-center">
                    <h2 class="mb-4 fw-bold">Trouvez l'établissement parfait</h2>
                    <form action="{{ route('search') }}" method="GET">
                        <div class="input-group mb-3">
                            <input type="text" name="query" class="form-control form-control-lg"
                                placeholder="Restaurant, hôtel, service..." value="{{ request('query') }}">
                            <button class="btn btn-primary px-4" type="submit">
                                <i class="fas fa-search me-2"></i>Rechercher
                            </button>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-advanced mt-3">
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
            <div class="col-xl-10">
                <div class="card advanced-search-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-search-plus me-2"></i>Recherche Avancée</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('search') }}" method="GET">
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Mots-clés</label>
                                    <input type="text" name="query" class="form-control" value="{{ request('query') }}"
                                        placeholder="Nom, description, service...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Type d'établissement</label>
                                    <select class="form-select" name="type_etablissement">
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
                            <div class="row g-4 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Ville</label>
                                    <input type="text" name="ville" class="form-control"
                                        value="{{ request('ville') }}" placeholder="Kinshasa, Goma...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Commune</label>
                                    <input type="text" name="commune" class="form-control"
                                        value="{{ request('commune') }}" placeholder="Gombe, Kalamu...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Quartier</label>
                                    <input type="text" name="quartier" class="form-control"
                                        value="{{ request('quartier') }}" placeholder="Yolo, Matonge...">
                                </div>
                            </div>
                            <div class="row g-4 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Note minimale</label>
                                    <select class="form-select" name="note_min">
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
                                    <label class="form-label">Budget (max)</label>
                                    <div class="input-group">
                                        <input type="number" name="budget_max" class="form-control"
                                            value="{{ request('budget_max') }}" placeholder="Montant maximum">
                                        <span class="input-group-text">$</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check mt-4 pt-2">
                                        <input class="form-check-input" type="checkbox" name="has_promotion"
                                            id="hasPromotion" value="1"
                                            {{ request('has_promotion') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="hasPromotion">
                                            Avec promotions actives
                                        </label>
                                    </div>
                                </div>
                            </div>
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
@endsection

@section('scripts')
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
                let currentIndex = 0;
                const slideWidth = slides[0].offsetWidth + 32; // inclut le gap (2rem = 32px)

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
                        updateCarousel();
                    }
                });

                nextBtn.addEventListener('click', () => {
                    if (currentIndex < slideCount - 1) {
                        currentIndex++;
                        updateCarousel();
                    }
                });

                function updateCarousel() {
                    const offset = -currentIndex * slideWidth;
                    track.style.transform = `translateX(${offset}px)`;
                    updateIndicators();
                }

                function updateIndicators() {
                    document.querySelectorAll('.indicator').forEach((ind, index) => {
                        ind.classList.toggle('active', index === currentIndex);
                    });
                }

                function goToSlide(index) {
                    currentIndex = index;
                    updateCarousel();
                }

                // Auto-rotation avec pause au survol
                let autoSlide = setInterval(() => {
                    if (currentIndex < slideCount - 1) {
                        currentIndex++;
                    } else {
                        currentIndex = 0;
                    }
                    updateCarousel();
                }, 5000);

                track.addEventListener('mouseenter', () => clearInterval(autoSlide));
                track.addEventListener('mouseleave', () => {
                    autoSlide = setInterval(() => {
                        if (currentIndex < slideCount - 1) {
                            currentIndex++;
                        } else {
                            currentIndex = 0;
                        }
                        updateCarousel();
                    }, 5000);
                });

                // Initialiser le carrousel
                updateCarousel();

                // Redimensionnement
                window.addEventListener('resize', () => {
                    const newSlideWidth = slides[0].offsetWidth + 32;
                    const offset = -currentIndex * newSlideWidth;
                    track.style.transform = `translateX(${offset}px)`;
                });
            }

            // Recherche Avancée
            const advancedBtn = document.querySelector('.btn-advanced');
            const advancedSearch = document.getElementById('advancedSearch');
            const hideAdvanced = document.getElementById('hideAdvanced');

            if (advancedBtn && advancedSearch && hideAdvanced) {
                advancedBtn.addEventListener('click', function() {
                    advancedSearch.style.display = 'block';
                    setTimeout(() => {
                        advancedSearch.style.opacity = '1';
                        advancedSearch.style.transform = 'translateY(0)';
                    }, 10);
                    window.scrollTo({
                        top: advancedSearch.offsetTop - 50,
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
        });
    </script>
@endsection
