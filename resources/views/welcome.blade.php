@extends('layouts.app')

@section('styles')
    <style>
        :root {
            --primary-color: #245c48;
            --secondary-color: #c45c36;
            --accent-color: #c45c36;
            --dark-color: #202923;
            --light-color: #f5f6f1;
            --gradient-primary: #245c48;
        }

        body {
            background-color: #f5f6f1;
            font-family: 'Segoe UI', sans-serif;
            color: #202923;
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

        .home-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(220px, .6fr);
            gap: 2rem;
            align-items: center;
            margin: 1.5rem auto 2rem;
            padding: clamp(1.5rem, 5vw, 3.5rem);
            overflow: hidden;
            border-radius: 1.2rem;
            background: #203b30;
            color: white;
        }
        .home-hero-kicker { margin: 0 0 .8rem; color: #b9d9c6; font-size: .78rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
        .home-hero h1 { max-width: 720px; margin: 0; color: #fff; font: 600 clamp(2.1rem, 5vw, 3.8rem)/1.05 Georgia, serif; }
        .home-hero-copy > p:not(.home-hero-kicker) { max-width: 640px; margin: 1rem 0 1.4rem; color: #d6e3da; line-height: 1.7; }
        .home-hero-actions { display: flex; flex-wrap: wrap; gap: .7rem; }
        .home-hero-actions .btn { min-height: 46px; display: inline-flex; align-items: center; justify-content: center; padding-inline: 1rem; border-radius: .65rem; font-weight: 700; }
        .home-hero-actions .btn-primary { border-color: #c45c36; background: #c45c36; }
        .home-hero-actions .btn-primary:hover { border-color: #a94628; background: #a94628; }
        .home-hero-actions .btn-outline-light:hover { color: #203b30; }
        .home-hero-stats { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; }
        .home-stat { min-height: 110px; display: flex; flex-direction: column; justify-content: space-between; padding: 1rem; border: 1px solid rgba(255,255,255,.22); border-radius: .85rem; background: rgba(255,255,255,.07); }
        .home-stat strong { font: 600 2rem Georgia, serif; color: #fff; }
        .home-stat span { color: #d6e3da; font-size: .8rem; }
        .home-shortcuts { display: flex; flex-wrap: wrap; gap: .65rem; margin: 0 auto 2rem; }
        .home-shortcut { min-height: 45px; display: inline-flex; align-items: center; gap: .55rem; padding: 0 .95rem; border: 1px solid #dce4dc; border-radius: .65rem; background: #fff; color: #245c48; font-weight: 700; text-decoration: none; }
        .home-shortcut:hover { border-color: #245c48; color: #194532; }
        .ad-carousel-container { margin-top: 1.5rem; padding: 1rem 0; border: 1px solid #e1e7e1; border-radius: 1rem; background: #fff; box-shadow: 0 8px 24px rgba(29,45,39,.06); }
        .ad-carousel-container::before { height: 0; background: transparent; }
        .ad-slide { border: 1px solid #e1e7e1; border-radius: .85rem; box-shadow: none; }
        .ad-slide::after { height: 0; }
        .ad-slide:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(29,45,39,.1); }
        .carousel-nav { width: 44px; height: 44px; color: #245c48; }
        .carousel-nav:hover { background: #245c48; }
        .search-container { max-width: none; margin: 0 auto; padding: clamp(1.4rem, 4vw, 2.4rem); border: 1px solid #e1e7e1; border-radius: 1rem; box-shadow: 0 8px 24px rgba(29,45,39,.06); }
        .search-container::before { height: 4px; background: #c45c36; }
        .search-container:hover { transform: none; box-shadow: 0 8px 24px rgba(29,45,39,.08); }
        .search-container h2 { color: #202923; font: 600 clamp(1.5rem, 3vw, 2rem) Georgia, serif; }
        .search-container h2::after { display: none; }
        .search-container .form-control, .advanced-search-card .form-control, .advanced-search-card .form-select { min-height: 46px; border: 1px solid #d8e0d9; border-radius: .65rem; box-shadow: none; }
        .search-container .btn, .advanced-search-card .btn { min-height: 46px; border-radius: .65rem; }
        .card-header { background: #245c48; border-radius: .8rem .8rem 0 0 !important; }
        .card-header h5 { font-size: 1.1rem; }
        .card-body { padding: clamp(1rem, 3vw, 1.75rem); }
        .article-reservation-form, .delivery-panel { padding: clamp(1rem, 2.5vw, 1.5rem); border: 1px solid #e1e7e1; border-radius: 1rem; background: #fff; box-shadow: 0 8px 24px rgba(29,45,39,.05); }
        .reservation-product, .delivery-product { overflow: hidden; border: 1px solid #e1e7e1; border-radius: .8rem; background: #fff; }
        .reservation-product-image, .delivery-product-image { height: 180px; overflow: hidden; background: #f1f3ee; }
        .reservation-product-image img, .delivery-product-image img { width: 100%; height: 100%; object-fit: cover; }
        .delivery-panel-heading { padding-bottom: .9rem; border-bottom: 1px solid #e1e7e1; }
        .delivery-checkout { padding-top: 1rem; border-top: 1px solid #e1e7e1; }
        .delivery-total { padding: .75rem 1rem; border-radius: .65rem; background: #f3f6f1; }
        .public-section-heading { margin-bottom: 1.25rem; }
        @media (max-width: 767px) {
            .home-hero { grid-template-columns: 1fr; gap: 1.5rem; margin-top: .8rem; border-radius: 1rem; }
            .home-hero-stats { grid-template-columns: 1fr 1fr; }
            .home-stat { min-height: 90px; }
            .home-shortcuts { display: grid; grid-template-columns: 1fr 1fr; }
            .home-shortcut { justify-content: center; padding-inline: .5rem; font-size: .85rem; }
            .reservation-product-image, .delivery-product-image { height: 150px; }
        }
        @media (prefers-reduced-motion: reduce) { .search-container, .ad-slide { animation: none !important; transition: none !important; } }
    </style>
@endsection

@section('content')
    <section class="home-hero" aria-labelledby="home-hero-title">
        <div class="home-hero-copy">
            <p class="home-hero-kicker">BISIKA · établissements & commerces</p>
            <h1 id="home-hero-title">Les bonnes adresses et leurs produits, réunis au même endroit.</h1>
            <p>Découvrez les établissements, explorez leurs produits et envoyez une demande de réservation ou de livraison en quelques étapes.</p>
            <div class="home-hero-actions">
                <a class="btn btn-primary" href="{{ route('products.index') }}">Explorer les produits</a>
                <a class="btn btn-outline-light" href="#home-search">Trouver un établissement</a>
            </div>
        </div>
        <div class="home-hero-stats" aria-label="Offre disponible">
            <div class="home-stat"><strong>{{ $reservationEtablissements->count() }}</strong><span>établissements actifs</span></div>
            <div class="home-stat"><strong>{{ $reservationProducts->count() }}</strong><span>produits référencés</span></div>
        </div>
    </section>

    <nav class="home-shortcuts" aria-label="Accès rapides">
        <a class="home-shortcut" href="{{ route('products.index') }}"><i class="fas fa-box-open" aria-hidden="true"></i>Catalogue produits</a>
        <a class="home-shortcut" href="#reservations-articles"><i class="fas fa-basket-shopping" aria-hidden="true"></i>Réserver un article</a>
        <a class="home-shortcut" href="#livraison"><i class="fas fa-truck" aria-hidden="true"></i>Commander en livraison</a>
        <a class="home-shortcut" href="#home-search"><i class="fas fa-magnifying-glass" aria-hidden="true"></i>Rechercher</a>
    </nav>

    <!-- Carrousel Publicitaire -->
    <div class="container mt-4">
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
    <div class="container" id="home-search">
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
            document.querySelectorAll('.delivery-order-form').forEach(form => {
                const choices = Array.from(form.querySelectorAll('.delivery-product-choice'));
                const quantities = Array.from(form.querySelectorAll('.delivery-product-quantity'));
                const paymentPlans = Array.from(form.querySelectorAll('[name="payment_plan"]'));
                const depositRow = form.querySelector('.delivery-deposit-row');
                const depositInput = form.querySelector('.delivery-deposit-amount');
                const totalLabel = form.querySelector('.delivery-total-value');
                const balanceHint = form.querySelector('.delivery-balance-hint');

                const calculate = () => {
                    let total = 0;
                    choices.forEach(choice => {
                        const quantityInput = choice.closest('.delivery-product')?.querySelector('.delivery-product-quantity');
                        if (!quantityInput) return;
                        quantityInput.disabled = !choice.checked;
                        if (choice.checked) {
                            const max = Number(quantityInput.dataset.max) || 0;
                            const quantity = Math.max(1, Math.min(max, Number(quantityInput.value) || 1));
                            quantityInput.value = quantity;
                            total += quantity * (Number(choice.dataset.price) || 0);
                        }
                    });

                    const isInstallments = form.querySelector('[name="payment_plan"]:checked')?.value === 'two_installments';
                    depositRow.hidden = !isInstallments;
                    depositInput.disabled = !isInstallments;
                    if (isInstallments) {
                        depositInput.max = total.toFixed(2);
                        if (!depositInput.dataset.edited || Number(depositInput.value) > total) {
                            depositInput.value = Math.min(50, total).toFixed(2);
                        }
                    }

                    totalLabel.textContent = total.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' CDF';
                    const balance = Math.max(0, total - (isInstallments ? Number(depositInput.value) || 0 : total));
                    balanceHint.textContent = isInstallments && total > 0
                        ? 'Solde restant après acompte : ' + balance.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' CDF'
                        : (total > 0 ? 'Paiement en une fois : ' + total.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' CDF' : 'Sélectionnez des articles pour afficher le total.');
                };

                choices.forEach(choice => choice.addEventListener('change', calculate));
                quantities.forEach(quantity => quantity.addEventListener('input', calculate));
                paymentPlans.forEach(plan => plan.addEventListener('change', calculate));
                depositInput.addEventListener('input', function() {
                    this.dataset.edited = 'true';
                    calculate();
                });

                form.addEventListener('submit', function(event) {
                    if (!choices.some(choice => choice.checked)) {
                        event.preventDefault();
                        window.BisikaAlerts.error('Sélectionnez au moins un article pour la livraison.');
                        return;
                    }

                    if (form.querySelector('[name="payment_plan"]:checked')?.value === 'two_installments') {
                        const total = choices.reduce((sum, choice) => {
                            if (!choice.checked) return sum;
                            const quantity = Number(choice.closest('.delivery-product')?.querySelector('.delivery-product-quantity')?.value) || 0;
                            return sum + quantity * (Number(choice.dataset.price) || 0);
                        }, 0);
                        const deposit = Number(depositInput.value) || 0;
                        if (deposit <= 0 || deposit > total) {
                            event.preventDefault();
                            window.BisikaAlerts.error('L’acompte doit être supérieur à zéro et ne peut pas dépasser le total.');
                        }
                    }
                });

                calculate();
            });

            document.querySelectorAll('.article-reservation-form').forEach(form => {
                form.querySelectorAll('.reservation-product-choice').forEach(choice => {
                    choice.addEventListener('change', function() {
                        const quantity = this.closest('.reservation-product')?.querySelector('.reservation-product-quantity');
                        if (!quantity) return;
                        quantity.disabled = !this.checked;
                        quantity.value = this.checked ? 1 : '';
                    });
                });

                form.addEventListener('submit', function(event) {
                    if (!this.querySelector('.reservation-product-choice:checked')) {
                        event.preventDefault();
                        window.BisikaAlerts.error('Sélectionnez au moins un article disponible.');
                    }
                });
            });

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
