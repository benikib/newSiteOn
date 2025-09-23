<!DOCTYPE html>
<html lang="fr">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Bisika - {{ $etablissement->nom }}</title>

    <!-- CSS optimisé pour mobile -->
    <style>
        /* =========================
           RESET & VARIABLES
        ========================= */
        :root {
            --primary: #0d6efd;
            --secondary: #6c757d;
            --success: #198754;
            --danger: #dc3545;
            --warning: #ffc107;
            --light: #f8f9fa;
            --dark: #212529;
            --border-radius: 12px;
            --shadow: 0 4px 12px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
            font-size: 16px;
            overflow-x: hidden;
            padding-bottom: 20px;
        }

        /* =========================
           HEADER & NAVIGATION
        ========================= */
        .header {
            background-color: #fff;
            padding: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
        }

        .header h1 {
            font-size: 1.4rem;
            margin: 0 auto;
            text-align: center;
            color: var(--primary);
        }

        .back-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--primary);
            padding: 5px;
        }

        /* =========================
           PUBLICITÉS CAROUSEL
        ========================= */
        .ad-container {
            position: relative;
            margin: 15px;
            border-radius: var(--border-radius);
            overflow: hidden;
            background: white;
            box-shadow: var(--shadow);
        }

        .ad-header {
            padding: 15px;
            background: var(--primary);
            color: white;
            text-align: center;
            font-weight: 600;
        }

        .ad-track {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }

        .ad-track::-webkit-scrollbar {
            display: none;
        }

        .ad-slide {
            flex: 0 0 100%;
            scroll-snap-align: start;
            padding: 10px;
        }

        .ad-image-wrapper {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
            height: 180px;
        }

        .ad-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ad-title {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            color: white;
            padding: 12px;
            font-size: 0.95rem;
        }

        .ad-indicators {
            display: flex;
            justify-content: center;
            gap: 8px;
            padding: 15px;
        }

        .ad-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ddd;
            border: none;
        }

        .ad-indicator.active {
            background: var(--primary);
        }

        /* =========================
           PROFILE CARD
        ========================= */
        .profile-card {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            margin: 15px;
        }

        .profile-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }

        .profile-img-container {
            position: relative;
            margin-bottom: 15px;
        }

        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .profile-name {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--dark);
        }

        .profile-description {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* Contact info */
        .contact-section {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .section-title {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: var(--primary);
        }

        .section-title i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .contact-item i {
            color: var(--primary);
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        /* Social links */
        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            padding: 15px 20px;
        }

        .social-links a {
            color: var(--primary);
            font-size: 1.2rem;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0f6ff;
            transition: var(--transition);
        }

        .social-links a:active {
            transform: scale(0.95);
        }

        /* =========================
           SERVICES SECTION
        ========================= */
        .services-section {
            padding: 20px;
        }

        .services-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .currency-toggle {
            display: flex;
            background: #f0f6ff;
            border-radius: 50px;
            padding: 4px;
        }

        .currency-btn {
            padding: 8px 15px;
            border: none;
            background: none;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .currency-btn.active {
            background: var(--primary);
            color: white;
        }

        .service-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }

        .service-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .service-name {
            font-weight: 600;
            color: var(--dark);
            font-size: 1.1rem;
        }

        .service-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .service-price {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--primary);
            margin-bottom: 15px;
        }

        .promo-badge {
            background: var(--danger);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 10px;
        }

        .reservation-btn {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: var(--transition);
        }

        .reservation-btn:active {
            transform: scale(0.98);
            background: #0b5ed7;
        }

        /* =========================
           ADDRESS SECTION
        ========================= */
        .address-section {
            padding: 20px;
            border-top: 1px solid #eee;
        }

        .address-item {
            display: flex;
            margin-bottom: 15px;
        }

        .address-item i {
            color: var(--primary);
            margin-right: 12px;
            margin-top: 3px;
            font-size: 1.1rem;
        }

        .itineraire-btn {
            padding: 10px 15px;
            background: white;
            border: 1px solid var(--primary);
            color: var(--primary);
            border-radius: 8px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: var(--transition);
        }

        .itineraire-btn i {
            margin-right: 8px;
        }

        .itineraire-btn:active {
            background: #f0f6ff;
        }

        /* =========================
           GALLERY SECTION
        ========================= */
        .gallery-section {
            margin: 15px;
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .gallery-header {
            padding: 15px;
            background: var(--primary);
            color: white;
            text-align: center;
            font-weight: 600;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            padding: 15px;
        }

        .gallery-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            aspect-ratio: 1;
        }

        .gallery-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .gallery-item:active .gallery-img {
            transform: scale(1.05);
        }

        /* =========================
           MODALS
        ========================= */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            padding: 15px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-container {
            background: white;
            border-radius: var(--border-radius);
            width: 100%;
            max-width: 500px;
            max-height: 90vh;
            overflow: hidden;
            transform: translateY(20px);
            transition: transform 0.3s ease;
        }

        .modal-overlay.active .modal-container {
            transform: translateY(0);
        }

        .modal-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-weight: 600;
            color: var(--dark);
            font-size: 1.2rem;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #999;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-body {
            padding: 15px;
            max-height: 60vh;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 15px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        /* Calendar styling */
        .calendar-container {
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }

        /* Gallery modal */
        .gallery-modal .modal-container {
            max-width: 100%;
            background: black;
        }

        .gallery-modal .modal-header {
            background: rgba(0,0,0,0.7);
            color: white;
            border: none;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 10;
        }

        .gallery-modal .modal-close {
            color: white;
        }

        .modal-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .modal-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 1.2rem;
            z-index: 10;
        }

        .modal-prev {
            left: 10px;
        }

        .modal-next {
            right: 10px;
        }

        /* =========================
           BUTTONS
        ========================= */
        .btn {
            padding: 12px 20px;
            border-radius: 8px;
            border: none;
            font-weight: 500;
            font-size: 1rem;
            transition: var(--transition);
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn:active {
            transform: scale(0.98);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-secondary {
            background: var(--secondary);
            color: white;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--primary);
            color: var(--primary);
        }

        /* =========================
           UTILITY CLASSES
        ========================= */
        .hidden {
            display: none !important;
        }

        .text-center {
            text-align: center;
        }

        .mt-2 { margin-top: 10px; }
        .mt-3 { margin-top: 15px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-3 { margin-bottom: 15px; }

        /* =========================
           RESPONSIVE DESIGN
        ========================= */
        @media (min-width: 576px) {
            .gallery-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .ad-slide {
                flex: 0 0 50%;
            }
        }

        @media (min-width: 768px) {
            body {
                font-size: 17px;
            }

            .profile-card {
                margin: 20px auto;
                max-width: 800px;
            }

            .gallery-section {
                margin: 20px auto;
                max-width: 800px;
            }

            .ad-container {
                margin: 20px auto;
                max-width: 800px;
            }

            .ad-slide {
                flex: 0 0 33.333%;
            }

            .gallery-grid {
                grid-template-columns: repeat(4, 1fr);
                gap: 15px;
                padding: 20px;
            }
        }

        /* Support for very small devices */
        @media (max-width: 350px) {
            .gallery-grid {
                grid-template-columns: 1fr;
            }

            .profile-img {
                width: 100px;
                height: 100px;
            }

            .service-header {
                flex-direction: column;
            }

            .promo-badge {
                margin-left: 0;
                margin-top: 5px;
                align-self: flex-start;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header class="header">
        <button class="back-btn" onclick="history.back()">←</button>
        <h1>Bisika</h1>
        <div style="width: 40px;"></div> <!-- Spacer for balance -->
    </header>

    <!-- Publicités Carousel -->
    @if(isset($photos) && $photos->count() > 0)
    <div class="ad-container">
        <div class="ad-header">Publicités</div>
        <div class="ad-track" id="adTrack">
            @foreach($photos as $pub)
            <div class="ad-slide">
                <a href="{{ route('ets.info', [$pub->etablissement_id]) }}" class="ad-link">
                    <div class="ad-image-wrapper">
                        <img src="{{ asset('storage/' . str_replace('public/','',$pub->image_path)) }}" class="ad-image" alt="{{ $pub->titre }}" loading="lazy" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjE1MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9IjAuMzVlbSI+SW1hZ2Ugbm90IGZvdW5kPC90ZXh0Pjwvc3ZnPg==';">
                        @if($pub->titre)
                        <div class="ad-title">{{ $pub->titre }}</div>
                        @endif
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        <div class="ad-indicators" id="adIndicators">
            @foreach($photos as $index => $pub)
            <button class="ad-indicator {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}"></button>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Profile Card -->
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-img-container">
                @if($etablissement->photos->isNotEmpty())
                <img src="{{ asset('storage/' . str_replace('public/','',$etablissement->photos->first()->image_path)) }}" class="profile-img" alt="{{ $etablissement->photos->first()->titre }}" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9IjAuMzVlbSI+SW1hZ2Ugbm90IGZvdW5kPC90ZXh0Pjwvc3ZnPg==';">
                @else
                <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9IjAuMzVlbSI+SW1hZ2Ugbm90IGZvdW5kPC90ZXh0Pjwvc3ZnPg==" class="profile-img" alt="Photo par défaut">
                @endif
            </div>
            <h1 class="profile-name">{{ $etablissement->nom }}</h1>
            <p class="profile-description">{{ $etablissement->description }}</p>
        </div>

        <!-- Contact Section -->
        <div class="contact-section">
            <div class="section-title">
                <i class="fas fa-phone-alt"></i>
                <h2>Contact</h2>
            </div>
            <div class="contact-item">
                <i class="fas fa-phone"></i>
                <span>{{ $etablissement->telephone ?? 'Non renseigné' }}</span>
            </div>
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <span>{{ $etablissement->email ?? 'Non renseigné' }}</span>
            </div>
            @if($etablissement->website)
            <div class="contact-item">
                <i class="fas fa-globe"></i>
                <a href="{{ $etablissement->website }}" target="_blank" style="color: var(--primary);">{{ $etablissement->website }}</a>
            </div>
            @endif
        </div>

        <!-- Social Links -->
        <div class="social-links">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
        </div>

        <!-- Services Section -->
        <div class="services-section">
            <div class="services-header">
                <h2 class="section-title">Services</h2>
                <div class="currency-toggle">
                    <button class="currency-btn" id="btn-usd">USD</button>
                    <button class="currency-btn active" id="btn-cdf">CDF</button>
                </div>
            </div>

            @forelse($etablissement->services as $service)
            @php
                $prixInitial = $service->prix;
                $promotion = $service->promotion ?? 0;
                $dateFinPromo = $service->date_fin_promo ?? null;
                $promoValide = $promotion > 0 && (!$dateFinPromo || \Carbon\Carbon::parse($dateFinPromo)->isFuture());
                $reductionUSD = $promoValide ? ($prixInitial * $promotion) / 100 : 0;
                $prixUSD = $prixInitial - $reductionUSD;
                $taux = \App\Models\TauxDeChange::where('date', today())->first()?->usd_cdf ?? 2500;
                $prixCDF = $prixInitial * $taux - ($promoValide ? ($prixInitial * $taux * $promotion) / 100 : 0);
                $datesConfirmees = $service->reservations->where('statut','confirmé')->pluck('date')->map(fn($d)=>is_string($d)?$d:$d->format('Y-m-d'))->values();
            @endphp

            <div class="service-card">
                <div class="service-header">
                    <div class="service-name">{{ $service->nom }}</div>
                    @if($promoValide)
                    <span class="promo-badge">Promo -{{ $promotion }}%</span>
                    @endif
                </div>
                <p class="service-description">{{ $service->description ?? 'Aucune description disponible' }}</p>
                <div class="service-price">
                    <span class="usd-price hidden">{{ number_format($prixUSD, 2) }} $</span>
                    <span class="cdf-price">{{ number_format($prixCDF, 0) }} CDF</span>
                </div>
                <button class="reservation-btn" onclick="openReservationModal({{ $service->id }}, '{{ $service->nom }}', {{ json_encode($datesConfirmees) }})">
                    Réserver
                </button>
            </div>
            @empty
            <div class="text-center" style="padding: 20px; color: #666;">
                Aucun service disponible
            </div>
            @endforelse
        </div>

        <!-- Address Section -->
        <div class="address-section">
            <div class="section-title">
                <i class="fas fa-map-marker-alt"></i>
                <h2>Adresse</h2>
            </div>
            <div class="address-item">
                <i class="fas fa-city"></i>
                <div>
                    <div>{{ $etablissement->ville }}, commune : {{ $etablissement->commune }}</div>
                    <div class="mt-2">Av. {{ $etablissement->avenue }}, N° {{ $etablissement->numero }}</div>
                </div>
            </div>
            <button class="itineraire-btn" onclick="showMessage('Fonctionnalité en développement', 'Cette fonctionnalité n\'est pas encore disponible. Revenez bientôt !')">
                <i class="fas fa-route"></i> Itinéraire
            </button>
        </div>
    </div>

    <!-- Gallery Section -->
    <div class="gallery-section">
        <div class="gallery-header">Galerie Photo</div>
        <div class="gallery-grid">
            @forelse($etablissement->photos as $photo)
            <div class="gallery-item" onclick="openGalleryModal({{ $loop->index }})">
                <img src="{{ asset('storage/' . str_replace('public/','',$photo->image_path)) }}" class="gallery-img" alt="{{ $photo->titre }}" loading="lazy" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjE1MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9IjAuMzVlbSI+SW1hZ2Ugbm90IGZvdW5kPC90ZXh0Pjwvc3ZnPg==';">
            </div>
            @empty
            <div class="text-center" style="grid-column: 1 / -1; padding: 20px; color: #666;">
                Aucune photo disponible
            </div>
            @endforelse
        </div>
    </div>

    <!-- Reservation Modal -->
    <div class="modal-overlay" id="reservationModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="modal-title" id="reservationModalTitle">Réservation</h2>
                <button class="modal-close" onclick="closeModal('reservationModal')">×</button>
            </div>
            <div class="modal-body">
                <div class="calendar-container" id="calendarContainer"></div>
                <input type="hidden" id="selectedDate">
                <div class="form-group">
                    <label class="form-label">Nom complet</label>
                    <input type="text" class="form-input" id="clientName" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" class="form-input" id="clientPhone" required>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('reservationModal')">Annuler</button>
                <button class="btn btn-primary" onclick="submitReservation()">Confirmer</button>
            </div>
        </div>
    </div>

    <!-- Message Modal -->
    <div class="modal-overlay" id="messageModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="modal-title" id="messageModalTitle">Message</h2>
                <button class="modal-close" onclick="closeModal('messageModal')">×</button>
            </div>
            <div class="modal-body">
                <p id="messageModalText"></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="closeModal('messageModal')">OK</button>
            </div>
        </div>
    </div>

    <!-- Gallery Modal -->
    <div class="modal-overlay gallery-modal" id="galleryModal">
        <div class="modal-container">
            <div class="modal-header">
                <h2 class="modal-title" id="galleryModalTitle">Galerie</h2>
                <button class="modal-close" onclick="closeModal('galleryModal')">×</button>
            </div>
            <div class="modal-body" style="padding: 0; display: flex; align-items: center; justify-content: center; height: 60vh;">
                <img id="modalImage" class="modal-image" src="" alt="">
                <button class="modal-nav modal-prev" onclick="prevImage()">❮</button>
                <button class="modal-nav modal-next" onclick="nextImage()">❯</button>
            </div>
        </div>
    </div>

    <script>
        // =========================
        // GLOBAL VARIABLES
        // =========================
        let currentServiceId = null;
        let currentImageIndex = 0;
        const photos = @json($etablissement->photos);

        // =========================
        // CURRENCY TOGGLE
        // =========================
        document.getElementById('btn-usd').addEventListener('click', function() {
            this.classList.add('active');
            document.getElementById('btn-cdf').classList.remove('active');
            document.querySelectorAll('.usd-price').forEach(el => el.classList.remove('hidden'));
            document.querySelectorAll('.cdf-price').forEach(el => el.classList.add('hidden'));
        });

        document.getElementById('btn-cdf').addEventListener('click', function() {
            this.classList.add('active');
            document.getElementById('btn-usd').classList.remove('active');
            document.querySelectorAll('.usd-price').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.cdf-price').forEach(el => el.classList.remove('hidden'));
        });

        // =========================
        // CAROUSEL FUNCTIONALITY
        // =========================
        @if(isset($photos) && $photos->count() > 0)
        const track = document.getElementById('adTrack');
        const indicators = document.querySelectorAll('.ad-indicator');
        let currentIndex = 0;

        // Auto-advance carousel
        setInterval(() => {
            currentIndex = (currentIndex + 1) % {{ $photos->count() }};
            updateCarousel();
        }, 5000);

        // Indicator clicks
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                currentIndex = index;
                updateCarousel();
            });
        });

        // Swipe functionality for touch devices
        let startX = 0;
        track.addEventListener('touchstart', e => {
            startX = e.touches[0].clientX;
        });

        track.addEventListener('touchend', e => {
            const endX = e.changedTouches[0].clientX;
            const diff = startX - endX;

            if (Math.abs(diff) > 50) { // Minimum swipe distance
                if (diff > 0 && currentIndex < {{ $photos->count() - 1 }}) {
                    // Swipe left - next
                    currentIndex++;
                } else if (diff < 0 && currentIndex > 0) {
                    // Swipe right - previous
                    currentIndex--;
                }
                updateCarousel();
            }
        });

        function updateCarousel() {
            track.scrollTo({
                left: track.clientWidth * currentIndex,
                behavior: 'smooth'
            });

            indicators.forEach((indicator, index) => {
                indicator.classList.toggle('active', index === currentIndex);
            });
        }
        @endif

        // =========================
        // MODAL FUNCTIONS
        // =========================
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            document.body.style.overflow = '';
        }

        function showMessage(title, message) {
            document.getElementById('messageModalTitle').textContent = title;
            document.getElementById('messageModalText').textContent = message;
            openModal('messageModal');
        }

        // =========================
        // RESERVATION FUNCTIONS
        // =========================
        function openReservationModal(serviceId, serviceName, disabledDates) {
            currentServiceId = serviceId;
            document.getElementById('reservationModalTitle').textContent = `Réservation - ${serviceName}`;

            // Create a simple date input instead of complex calendar
            const today = new Date().toISOString().split('T')[0];
            const calendarHtml = `
                <label class="form-label">Choisissez une date</label>
                <input type="date" class="form-input" id="dateInput" min="${today}" required>
                <div style="font-size: 0.8rem; color: #666; margin-top: 5px;">
                    Les dates grisées sont déjà réservées
                </div>
            `;

            document.getElementById('calendarContainer').innerHTML = calendarHtml;

            // Simple validation to prevent selecting disabled dates
            document.getElementById('dateInput').addEventListener('change', function() {
                const selectedDate = this.value;
                if (disabledDates.includes(selectedDate)) {
                    showMessage('Date indisponible', 'Cette date est déjà réservée. Veuillez en choisir une autre.');
                    this.value = '';
                }
            });

            // Clear previous inputs
            document.getElementById('clientName').value = '';
            document.getElementById('clientPhone').value = '';

            openModal('reservationModal');
        }

       async function submitReservation(serviceId) {
    const date = document.getElementById('dateInput').value;
    const name = document.getElementById('clientName').value.trim();
    const phone = document.getElementById('clientPhone').value.trim();

    if (!date || !name || !phone) {
        showMessage('Erreur', 'Veuillez remplir tous les champs obligatoires.');
        return;
    }

    try {
        // 👉 Appel API
        const response = await fetch('/reservations', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content // si Laravel
            },
            body: JSON.stringify({
                service_id: currentServiceId,
                date: date,
                client_name: name,
                client_phone: phone
            })
        });

        if (response.ok) {
            const data = await response.json();
            showMessage('Succès', data.message || 'Votre réservation a été envoyée avec succès!');
            setTimeout(() => {
                closeModal('reservationModal');
            }, 2000);
        } else {
            const error = await response.json();
            showMessage('Erreur', error.message || 'Une erreur est survenue lors de la réservation.');
        }
    } catch (err) {
        console.error(err);
        showMessage('Erreur', 'Impossible de contacter le serveur.');
    }
}


        // =========================
        // GALLERY FUNCTIONS
        // =========================
        function openGalleryModal(index) {
            if (!photos || photos.length === 0) return;

            currentImageIndex = index;
            updateGalleryImage();
            openModal('galleryModal');
        }

        function updateGalleryImage() {
            if (photos[currentImageIndex]) {
                const imagePath = photos[currentImageIndex].image_path.replace('public/', '');
                document.getElementById('modalImage').src = `/storage/${imagePath}`;
                document.getElementById('galleryModalTitle').textContent = photos[currentImageIndex].titre || 'Image';
            }
        }

        function prevImage() {
            if (!photos || photos.length === 0) return;
            currentImageIndex = (currentImageIndex - 1 + photos.length) % photos.length;
            updateGalleryImage();
        }

        function nextImage() {
            if (!photos || photos.length === 0) return;
            currentImageIndex = (currentImageIndex + 1) % photos.length;
            updateGalleryImage();
        }

        // Keyboard navigation for gallery
        document.addEventListener('keydown', function(e) {
            if (document.getElementById('galleryModal').classList.contains('active')) {
                if (e.key === 'ArrowLeft') prevImage();
                else if (e.key === 'ArrowRight') nextImage();
                else if (e.key === 'Escape') closeModal('galleryModal');
            }
        });

        // =========================
        // TOUCH GESTURES FOR GALLERY
        // =========================
        let touchStartX = 0;
        const galleryImage = document.getElementById('modalImage');

        if (galleryImage) {
            galleryImage.addEventListener('touchstart', e => {
                touchStartX = e.touches[0].clientX;
            });

            galleryImage.addEventListener('touchend', e => {
                const touchEndX = e.changedTouches[0].clientX;
                const diff = touchStartX - touchEndX;

                if (Math.abs(diff) > 50) { // Minimum swipe distance
                    if (diff > 0) nextImage(); // Swipe left - next
                    else prevImage(); // Swipe right - previous
                }
            });
        }
    </script>

    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
