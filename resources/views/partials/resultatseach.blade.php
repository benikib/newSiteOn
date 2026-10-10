<!DOCTYPE html>
<html lang="fr">
<head>
    @if (request()->routeIs('ets.info') && app()->environment('production') && config('services.analytics.ga4_measurement_id'))
        @include('partials.analytics-consent')
    @endif
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $etablissement->nom }} | Bisika</title>

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
    <style>
        :root {
            --primary: #245c48;
            --secondary: #c45c36;
            --success: #277353;
            --light: #f7f6f1;
            --dark: #202923;
            --border-radius: 16px;
            --shadow: 0 12px 36px rgba(32, 41, 35, .09);
            --shop-ink: #202923;
            --shop-muted: #68736d;
            --shop-paper: #f7f6f1;
            --shop-line: #e5e8e2;
            --shop-green: #245c48;
            --shop-orange: #c45c36;
            --shop-white: #fff;
        }

        html { scroll-behavior: smooth; scroll-padding-top: 130px; }
        [hidden] { display: none !important; }
        body { padding: 0; background: var(--shop-paper); color: var(--shop-ink); font-family: "Trebuchet MS", "Segoe UI", sans-serif; }
        button, input, select, textarea { font: inherit; }
        button, a { -webkit-tap-highlight-color: transparent; }
        button:focus-visible, a:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible {
            outline: 3px solid #d9835f; outline-offset: 3px;
        }
        .shop-shell { width: min(1180px, calc(100% - 32px)); margin: 0 auto; }
        .shop-header {
            position: sticky; top: 0; z-index: 100; background: rgba(255,255,255,.97);
            border-bottom: 1px solid var(--shop-line); box-shadow: 0 4px 18px rgba(32,41,35,.04);
        }
        .shop-header-inner { width: min(1180px, calc(100% - 32px)); margin: auto; }
        .shop-topbar { min-height: 68px; display: flex; align-items: center; gap: 14px; }
        .shop-brand { min-width: 0; display: flex; align-items: center; gap: 11px; color: var(--shop-ink); text-decoration: none; }
        .shop-logo { width: 42px; height: 42px; flex: 0 0 42px; border-radius: 13px; object-fit: cover; background: #e7eee8; }
        .shop-monogram { display: grid; place-items: center; color: white; background: var(--shop-green); font: 700 18px Georgia, serif; }
        .shop-brand-name { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 700; }
        .shop-nav { display: flex; align-items: center; gap: 22px; margin-left: auto; }
        .shop-nav a { color: var(--shop-muted); text-decoration: none; font-size: 14px; font-weight: 600; }
        .shop-nav a:hover { color: var(--shop-green); }
        .shop-icon-button, .shop-burger {
            min-width: 46px; min-height: 46px; display: inline-flex; justify-content: center; align-items: center;
            gap: 8px; border: 1px solid var(--shop-line); border-radius: 13px; background: white; color: var(--shop-ink); cursor: pointer;
        }
        .shop-icon-button svg, .shop-burger svg { width: 21px; height: 21px; }
        .shop-cart-count { min-width: 21px; height: 21px; display: inline-grid; place-items: center; padding: 0 5px; border-radius: 20px; background: var(--shop-orange); color: white; font-size: 12px; font-weight: 700; }
        .shop-burger { display: none; }
        .shop-search-row { padding: 0 0 13px; }
        .shop-search-wrap { position: relative; }
        .shop-search { width: 100%; min-height: 48px; padding: 0 48px 0 44px; border: 1px solid var(--shop-line); border-radius: 13px; background: #f8f9f6; color: var(--shop-ink); }
        .shop-search-icon { position: absolute; top: 14px; left: 15px; width: 20px; height: 20px; color: var(--shop-muted); pointer-events: none; }
        .shop-suggestions { position: absolute; top: calc(100% + 6px); left: 0; right: 0; z-index: 20; display: none; padding: 6px; border: 1px solid var(--shop-line); border-radius: 12px; background: white; box-shadow: var(--shadow); }
        .shop-suggestions.is-open { display: grid; }
        .shop-suggestion { display: flex; justify-content: space-between; gap: 12px; min-height: 44px; padding: 10px 12px; border: 0; border-radius: 8px; background: transparent; text-align: left; cursor: pointer; }
        .shop-suggestion:hover { background: var(--shop-paper); }
        .shop-suggestion small { color: var(--shop-muted); }
        .shop-main { padding-bottom: 64px; }
        .shop-hero { position: relative; min-height: 335px; display: flex; align-items: end; overflow: hidden; margin-top: 24px; border-radius: 22px; background: #315947; color: white; isolation: isolate; }
        .shop-hero-media, .shop-hero-shade { position: absolute; inset: 0; z-index: -1; }
        .shop-hero-media { width: 100%; height: 100%; object-fit: cover; }
        .shop-hero-shade { background: linear-gradient(90deg, rgba(17,38,29,.88), rgba(17,38,29,.48) 57%, rgba(17,38,29,.12)); }
        .shop-hero-content { width: min(740px, 100%); padding: clamp(26px, 6vw, 58px); }
        .shop-eyebrow { margin: 0 0 12px; color: #d6e8d9; font-size: 12px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
        .shop-hero h1 { max-width: 680px; margin: 0; font: 600 clamp(34px, 6vw, 56px)/1.04 Georgia, serif; }
        .shop-hero p { max-width: 560px; margin: 16px 0 22px; color: rgba(255,255,255,.88); line-height: 1.65; }
        .shop-hero-actions { display: flex; flex-wrap: wrap; gap: 10px; }
        .shop-button { min-height: 46px; display: inline-flex; justify-content: center; align-items: center; gap: 9px; padding: 0 17px; border: 1px solid transparent; border-radius: 11px; font-weight: 700; text-decoration: none; cursor: pointer; transition: transform .18s ease, background .18s ease; }
        .shop-button:hover { transform: translateY(-1px); }
        .shop-button-primary { color: white; background: var(--shop-orange); }
        .shop-button-light { color: var(--shop-ink); background: white; }
        .shop-button-outline { color: var(--shop-green); background: white; border-color: var(--shop-line); }
        .shop-section { margin-top: 52px; }
        .shop-section-heading { display: flex; justify-content: space-between; align-items: end; gap: 14px; margin-bottom: 18px; }
        .shop-section-heading h2 { margin: 0; font: 600 29px/1.2 Georgia, serif; }
        .shop-section-heading p { margin: 6px 0 0; color: var(--shop-muted); font-size: 14px; }
        .shop-service-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
        .shop-service-card { min-width: 0; padding: 21px; border: 1px solid var(--shop-line); border-radius: 15px; background: white; }
        .shop-service-icon { width: 42px; height: 42px; display: grid; place-items: center; margin-bottom: 14px; border-radius: 13px; background: #eaf1eb; color: var(--shop-green); }
        .shop-service-icon svg { width: 21px; height: 21px; }
        .shop-service-card h3 { margin: 0 0 7px; font-size: 16px; }
        .shop-service-card p { min-height: 42px; margin: 0 0 15px; color: var(--shop-muted); font-size: 14px; line-height: 1.5; }
        .shop-service-card .shop-button { width: 100%; }
        .shop-catalog { scroll-margin-top: 136px; }
        .shop-catalog-controls { display: grid; gap: 12px; margin-bottom: 15px; }
        .shop-categories { display: flex; gap: 8px; overflow-x: auto; padding: 2px 1px 6px; scrollbar-width: thin; }
        .shop-chip { min-height: 42px; flex: 0 0 auto; padding: 0 15px; border: 1px solid var(--shop-line); border-radius: 24px; background: white; color: var(--shop-muted); font-size: 14px; font-weight: 700; cursor: pointer; }
        .shop-chip.is-active, .shop-chip:hover { border-color: var(--shop-green); background: var(--shop-green); color: white; }
        .shop-filter-row { display: grid; grid-template-columns: minmax(0, 1fr) minmax(130px, .7fr) minmax(180px, 1fr) auto; gap: 10px; align-items: end; }
        .shop-filter { min-width: 0; }
        .shop-filter label { display: block; margin-bottom: 5px; color: var(--shop-muted); font-size: 12px; font-weight: 700; }
        .shop-filter select, .shop-filter input { width: 100%; min-height: 44px; padding: 0 11px; border: 1px solid var(--shop-line); border-radius: 10px; background: white; color: var(--shop-ink); }
        .shop-price-range { display: grid; grid-template-columns: 1fr 1fr; gap: 7px; }
        .shop-currency { display: flex; align-items: center; padding: 3px; border: 1px solid var(--shop-line); border-radius: 11px; background: white; }
        .shop-currency button { min-height: 38px; padding: 0 12px; border: 0; border-radius: 8px; background: transparent; color: var(--shop-muted); font-weight: 700; cursor: pointer; }
        .shop-currency button.is-active { background: var(--shop-green); color: white; }
        .shop-result-count { margin: 13px 0; color: var(--shop-muted); font-size: 13px; }
        .shop-product-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .shop-product-card { min-width: 0; overflow: hidden; border: 1px solid var(--shop-line); border-radius: 14px; background: white; transition: box-shadow .18s ease, transform .18s ease; }
        .shop-product-card:hover { transform: translateY(-2px); box-shadow: var(--shadow); }
        .shop-product-preview { display: block; width: 100%; padding: 0; border: 0; background: transparent; color: inherit; text-align: left; cursor: pointer; }
        .shop-product-image { position: relative; display: grid; place-items: center; overflow: hidden; aspect-ratio: 1.17; background: #eef0e9; }
        .shop-product-image img { width: 100%; height: 100%; object-fit: cover; }
        .shop-product-placeholder { width: 46px; height: 46px; color: #8da095; }
        .shop-stock-badge { position: absolute; top: 9px; left: 9px; padding: 6px 9px; border-radius: 20px; background: #e5f3e9; color: #245c48; font-size: 11px; font-weight: 700; }
        .shop-stock-low { background: #fff1db; color: #80551a; }
        .shop-stock-none { background: #fbe9e6; color: #8f3327; }
        .shop-product-copy { padding: 12px 12px 4px; }
        .shop-product-category { display: block; overflow: hidden; margin-bottom: 5px; color: var(--shop-muted); font-size: 11px; text-overflow: ellipsis; white-space: nowrap; }
        .shop-product-title { display: -webkit-box; overflow: hidden; min-height: 40px; margin: 0; font-size: 14px; line-height: 1.4; -webkit-box-orient: vertical; -webkit-line-clamp: 2; }
        .shop-product-price { margin: 8px 0 0; color: var(--shop-green); font-size: 17px; font-weight: 800; }
        .shop-product-unit { color: var(--shop-muted); font-size: 11px; font-weight: 500; }
        .shop-product-actions { padding: 8px 12px 12px; }
        .shop-product-actions .shop-button { width: 100%; min-height: 44px; padding: 0 9px; font-size: 13px; }
        .shop-empty { grid-column: 1 / -1; padding: 34px 18px; border: 1px dashed #ccd5ce; border-radius: 15px; background: white; text-align: center; }
        .shop-empty h3 { margin: 0 0 6px; font: 600 22px Georgia, serif; }
        .shop-empty p { margin: 0 0 14px; color: var(--shop-muted); }
        .shop-empty-categories { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; }
        .shop-more { display: flex; justify-content: center; margin-top: 22px; }
        .shop-gallery { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 10px; }
        .shop-gallery-button { overflow: hidden; aspect-ratio: 1.15; padding: 0; border: 0; border-radius: 12px; background: #e9ece6; cursor: pointer; }
        .shop-gallery-button img { width: 100%; height: 100%; object-fit: cover; transition: transform .2s ease; }
        .shop-gallery-button:hover img { transform: scale(1.04); }
        .shop-contact-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .shop-contact-block { padding: 21px; border: 1px solid var(--shop-line); border-radius: 15px; background: white; }
        .shop-contact-block h3 { margin: 0 0 12px; font-size: 16px; }
        .shop-contact-block p, .shop-contact-block a { color: var(--shop-muted); line-height: 1.7; overflow-wrap: anywhere; }
        .shop-contact-block a { text-decoration-color: #a9b4ac; }
        .shop-socials { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 12px; }
        .shop-socials a { min-height: 42px; display: inline-flex; align-items: center; padding: 0 12px; border: 1px solid var(--shop-line); border-radius: 10px; color: var(--shop-green); text-decoration: none; }
        .shop-footer { padding: 32px 0; background: #203b30; color: #f3f5f2; }
        .shop-footer-inner { display: flex; justify-content: space-between; gap: 22px; }
        .shop-footer p { margin: 0; color: #c6d2ca; font-size: 13px; line-height: 1.7; }
        .shop-footer-links { display: flex; flex-wrap: wrap; gap: 16px; align-items: start; }
        .shop-footer a { color: #f3f5f2; text-decoration-color: #94a99a; }
        .shop-promo-track { display: flex; gap: 12px; overflow-x: auto; padding: 4px 0 10px; scroll-snap-type: x mandatory; }
        .shop-promo-slide { position: relative; flex: 0 0 min(82%, 380px); overflow: hidden; aspect-ratio: 2.2; border-radius: 14px; scroll-snap-align: start; background: #e6eae4; }
        .shop-promo-slide img { width: 100%; height: 100%; object-fit: cover; }
        .shop-promo-slide span { position: absolute; inset: auto 0 0; padding: 25px 14px 12px; background: linear-gradient(transparent, rgba(0,0,0,.72)); color: white; }
        .shop-notice { margin: 18px auto 0; padding: 13px 16px; border-radius: 11px; background: #e5f3e9; color: #1d573e; }
        .shop-notice-error { background: #fbe9e6; color: #8f3327; }
        .shop-dialog { width: min(540px, calc(100% - 24px)); max-height: min(88vh, 780px); padding: 0; border: 0; border-radius: 18px; background: white; color: var(--shop-ink); box-shadow: 0 24px 70px rgba(0,0,0,.26); }
        .shop-dialog::backdrop { background: rgba(19,31,24,.55); backdrop-filter: blur(2px); }
        .shop-dialog-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 15px 18px; border-bottom: 1px solid var(--shop-line); }
        .shop-dialog-head h2 { margin: 0; font: 600 21px Georgia, serif; }
        .shop-dialog-body { max-height: calc(88vh - 70px); overflow-y: auto; padding: 18px; }
        .shop-close { width: 44px; height: 44px; flex: 0 0 44px; border: 0; border-radius: 12px; background: #f1f3ef; color: var(--shop-ink); font-size: 24px; cursor: pointer; }
        .shop-detail-image { display: block; width: 100%; max-height: 310px; margin-bottom: 14px; border-radius: 12px; object-fit: contain; background: #eef0e9; }
        .shop-detail-description { color: var(--shop-muted); line-height: 1.65; white-space: pre-line; }
        .shop-cart-layer { position: fixed; inset: 0; z-index: 300; visibility: hidden; background: rgba(19,31,24,.48); opacity: 0; transition: opacity .2s ease, visibility .2s ease; }
        .shop-cart-layer.is-open { visibility: visible; opacity: 1; }
        .shop-cart-panel { position: absolute; inset: 0 0 0 auto; width: min(460px, 100%); display: flex; flex-direction: column; transform: translateX(100%); background: white; transition: transform .24s ease; }
        .shop-cart-layer.is-open .shop-cart-panel { transform: translateX(0); }
        .shop-cart-items { flex: 1; overflow-y: auto; padding: 14px 18px; }
        .shop-cart-line { display: grid; grid-template-columns: minmax(0,1fr) auto; gap: 10px; padding: 13px 0; border-bottom: 1px solid var(--shop-line); }
        .shop-cart-line h3 { margin: 0 0 4px; font-size: 14px; }
        .shop-cart-line p { margin: 0; color: var(--shop-muted); font-size: 13px; }
        .shop-quantity { display: flex; align-items: center; gap: 6px; }
        .shop-quantity button { width: 36px; height: 36px; border: 1px solid var(--shop-line); border-radius: 9px; background: white; cursor: pointer; }
        .shop-cart-form { padding: 16px 18px; border-top: 1px solid var(--shop-line); }
        .shop-cart-form label, .shop-service-form label { display: block; margin: 10px 0 5px; font-size: 13px; font-weight: 700; }
        .shop-cart-form input, .shop-cart-form textarea, .shop-service-form input { width: 100%; min-height: 44px; padding: 10px 12px; border: 1px solid var(--shop-line); border-radius: 10px; }
        .shop-cart-form textarea { min-height: 76px; resize: vertical; }
        .shop-cart-form .shop-button { width: 100%; margin-top: 12px; }
        .shop-form-error { margin: 6px 0 0; color: #8f3327; font-size: 13px; }
        .shop-service-form { padding: 18px; }
        .shop-service-form .shop-button { width: 100%; margin-top: 14px; }
        .shop-live-message { min-height: 24px; margin-top: 9px; color: var(--shop-green); font-size: 14px; }
        .shop-whatsapp-fab {
            position: fixed; right: max(16px, env(safe-area-inset-right)); bottom: calc(20px + env(safe-area-inset-bottom)); z-index: 90;
            width: 58px; height: 58px; display: grid; place-items: center; border: 0; border-radius: 50%;
            background: #20bd5a; color: white; box-shadow: 0 8px 22px rgba(15, 108, 51, .3);
            animation: shop-whatsapp-in .4s ease-out both; transition: transform .2s ease, box-shadow .2s ease;
        }
        .shop-whatsapp-fab:hover { transform: translateY(-3px) scale(1.03); box-shadow: 0 11px 26px rgba(15, 108, 51, .38); }
        .shop-whatsapp-fab svg { width: 30px; height: 30px; fill: currentColor; }
        .shop-detail-whatsapp { width: 100%; margin-top: 10px; background: #20bd5a; color: white; }
        @keyframes shop-whatsapp-in { from { opacity: 0; transform: translateY(8px) scale(.94); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .shop-sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
        .shop-card-hidden { display: none !important; }
        @media (min-width: 700px) { .shop-product-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; } }
        @media (min-width: 1050px) { .shop-product-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 18px; } }
        @media (max-width: 760px) {
            .shop-topbar { min-height: 62px; gap: 8px; }
            .shop-brand { margin-right: auto; }
            .shop-brand-name { max-width: 46vw; }
            .shop-burger { display: inline-flex; order: 3; }
            .shop-nav { position: absolute; top: 61px; left: 0; right: 0; display: none; align-items: stretch; gap: 0; padding: 8px 16px 12px; border-bottom: 1px solid var(--shop-line); background: white; }
            .shop-nav.is-open { display: grid; }
            .shop-nav a { min-height: 44px; display: flex; align-items: center; padding: 0 8px; border-bottom: 1px solid #f0f1ee; }
            .shop-cart-label { display: none; }
            .shop-filter-row { grid-template-columns: 1fr 1fr; }
            .shop-filter-price { grid-column: 1 / -1; }
            .shop-currency { justify-self: start; }
            .shop-service-grid { grid-template-columns: 1fr; }
            .shop-service-card { display: grid; grid-template-columns: 44px minmax(0,1fr); column-gap: 13px; padding: 16px; }
            .shop-service-icon { grid-row: span 3; margin: 0; }
            .shop-service-card h3 { align-self: center; }
            .shop-service-card p { min-height: 0; margin-bottom: 10px; }
            .shop-service-card .shop-button { grid-column: 2; }
            .shop-hero { min-height: 360px; margin-top: 14px; border-radius: 17px; }
            .shop-hero-shade { background: linear-gradient(0deg, rgba(17,38,29,.89), rgba(17,38,29,.2)); }
            .shop-section { margin-top: 40px; }
            .shop-gallery { grid-template-columns: repeat(3, minmax(0,1fr)); }
            .shop-contact-grid { grid-template-columns: 1fr; }
            .shop-footer-inner { flex-direction: column; }
        }
        @media (max-width: 390px) {
            .shop-shell, .shop-header-inner { width: calc(100% - 24px); }
            .shop-topbar { gap: 6px; }
            .shop-logo { width: 38px; height: 38px; flex-basis: 38px; }
            .shop-brand { gap: 7px; }
            .shop-brand-name { max-width: 40vw; font-size: 14px; }
            .shop-product-grid { gap: 9px; }
            .shop-product-copy { padding: 10px 9px 3px; }
            .shop-product-actions { padding: 7px 9px 9px; }
            .shop-product-title { font-size: 13px; }
            .shop-product-price { font-size: 15px; }
            .shop-stock-badge { top: 6px; left: 6px; padding: 5px 7px; font-size: 10px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; animation-duration: .01ms !important; }
        }
    </style>
</head>

<body>
    <svg aria-hidden="true" width="0" height="0" style="position:absolute;overflow:hidden">
        <symbol id="shop-search-icon" viewBox="0 0 24 24"><circle cx="10.8" cy="10.8" r="6.8" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="m16 16 5 5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
        <symbol id="shop-bag-icon" viewBox="0 0 24 24"><path d="M5 8h14l1 13H4L5 8Z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M9 9V6a3 3 0 0 1 6 0v3" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></symbol>
        <symbol id="shop-menu-icon" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></symbol>
        <symbol id="shop-box-icon" viewBox="0 0 24 24"><path d="m3 7 9-4 9 4v10l-9 4-9-4V7Z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="m3.5 7.3 8.5 4 8.5-4M12 12v8.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></symbol>
        <symbol id="shop-calendar-icon" viewBox="0 0 24 24"><rect x="4" y="6" width="16" height="15" rx="2" fill="none" stroke="currentColor" stroke-width="1.7"/><path d="M8 3v5m8-5v5M4 10h16" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></symbol>
        <symbol id="shop-truck-icon" viewBox="0 0 24 24"><path d="M3 6h11v11H3zM14 10h4l3 3v4h-7z" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="7.5" cy="18" r="1.7" fill="none" stroke="currentColor" stroke-width="1.7"/><circle cx="17.5" cy="18" r="1.7" fill="none" stroke="currentColor" stroke-width="1.7"/></symbol>
        <symbol id="shop-phone-icon" viewBox="0 0 24 24"><path d="M6.5 3.5 10 3l2 5-2.2 1.8a14 14 0 0 0 4.4 4.4L16 12l5 2-.5 3.5c-.2 1.4-1.5 2.4-2.9 2.2A16.8 16.8 0 0 1 4.3 6.4c-.2-1.4.8-2.7 2.2-2.9Z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></symbol>
        <symbol id="shop-star-icon" viewBox="0 0 24 24"><path d="m12 3 2.7 5.5 6 .9-4.3 4.2 1 6-5.4-2.9-5.4 2.9 1-6-4.3-4.2 6-.9L12 3Z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></symbol>
    </svg>
    @php
        $brandPhoto = $etablissement->photos->first();
        $brandImagePath = $brandPhoto?->image_path ?: $etablissement->image_path;
        $brandImageUrl = $brandImagePath ? asset('storage/' . ltrim(str_replace('public/', '', $brandImagePath), '/')) : null;
        $shopIcons = ['calendar' => 'shop-calendar-icon', 'truck' => 'shop-truck-icon', 'phone' => 'shop-phone-icon', 'star' => 'shop-star-icon'];
        $whatsappWelcomeMessage = $etablissement->whatsapp_message ?: 'Bonjour ' . $etablissement->nom . ', je souhaite avoir des informations.';
        $galleryPhotos = $etablissement->photos->map(function ($photo) {
            return [
                'src' => asset('storage/' . str_replace('public/', '', $photo->image_path)),
                'title' => $photo->titre,
            ];
        })->values();
    @endphp
    <header class="shop-header">
        <div class="shop-header-inner">
            <div class="shop-topbar">
                <a class="shop-brand" href="#accueil" aria-label="Accueil {{ $etablissement->nom }}">
                    @if($brandImageUrl)
                        <img class="shop-logo" src="{{ $brandImageUrl }}" alt="Logo de {{ $etablissement->nom }}">
                    @else
                        <span class="shop-logo shop-monogram" aria-hidden="true">{{ mb_substr($etablissement->nom, 0, 1) }}</span>
                    @endif
                    <span class="shop-brand-name">{{ $etablissement->nom }}</span>
                </a>
                <nav class="shop-nav" id="shop-navigation" aria-label="Navigation principale">
                    <a href="#services">Services</a>
                    <a href="#produits">Produits</a>
                    <a href="#contact">Contact</a>
                </nav>
                <button class="shop-icon-button" type="button" id="shop-cart-open" aria-label="Ouvrir le panier de réservation">
                    <svg aria-hidden="true"><use href="#shop-bag-icon"/></svg>
                    <span class="shop-cart-label">Panier</span>
                    <span class="shop-cart-count" id="shop-cart-count" aria-live="polite">0</span>
                </button>
                <button class="shop-burger" type="button" id="shop-menu-toggle" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="shop-navigation">
                    <svg aria-hidden="true"><use href="#shop-menu-icon"/></svg>
                </button>
            </div>
            <div class="shop-search-row">
                <div class="shop-search-wrap">
                    <svg class="shop-search-icon" aria-hidden="true"><use href="#shop-search-icon"/></svg>
                    <label class="shop-sr-only" for="shop-search">Rechercher un produit ou une catégorie</label>
                    <input class="shop-search" id="shop-search" type="search" autocomplete="off" placeholder="Rechercher un produit ou une catégorie..." aria-controls="shop-suggestions" aria-autocomplete="list">
                    <div class="shop-suggestions" id="shop-suggestions" role="listbox" aria-label="Suggestions de recherche"></div>
                </div>
            </div>
        </div>
    </header>

    <main class="shop-main" id="accueil">
        <div class="shop-shell">
            @if(session('success'))<div class="shop-notice" role="status">{{ session('success') }}</div>@endif
            @if($errors->any())<div class="shop-notice shop-notice-error" role="alert">{{ $errors->first() }}</div>@endif

            @if($photos->isNotEmpty())
                <section class="shop-section" aria-label="Publicités">
                    <div class="shop-promo-track">
                        @foreach($photos as $pub)
                            <a class="shop-promo-slide" href="{{ route('ets.info', [$pub->etablissement_id]) }}">
                                <img src="{{ asset('storage/' . str_replace('public/', '', $pub->image_path)) }}" alt="{{ $pub->titre ?: 'Publicité' }}" loading="lazy">
                                @if($pub->titre)<span>{{ $pub->titre }}</span>@endif
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="shop-hero" aria-labelledby="shop-hero-title">
                @if($brandImageUrl)<img class="shop-hero-media" src="{{ $brandImageUrl }}" alt="" fetchpriority="high">@endif
                <span class="shop-hero-shade" aria-hidden="true"></span>
                <div class="shop-hero-content">
                    <p class="shop-eyebrow">{{ $etablissement->typeEtablissement->nom ?? 'Établissement' }}</p>
                    <h1 id="shop-hero-title">{{ $etablissement->nom }}</h1>
                    <p>{{ $etablissement->description ?: 'Découvrez notre sélection et contactez-nous pour toute demande.' }}</p>
                    <div class="shop-hero-actions">
                        <a class="shop-button shop-button-primary" href="#produits">Voir les produits</a>
                        <button class="shop-button shop-button-light" type="button" id="shop-hero-reserve">Réserver</button>
                    </div>
                </div>
            </section>

            <section class="shop-section" id="services" aria-labelledby="shop-services-title">
                <div class="shop-section-heading">
                    <div><h2 id="shop-services-title">Nos services</h2><p>Des services proposés par l’établissement.</p></div>
                </div>
                <div class="shop-service-grid">
                    @forelse($etablissement->services as $service)
                        @php
                            $serviceDates = $service->reservations->where('statut', 'confirmé')->pluck('date')->map(fn ($date) => is_string($date) ? $date : $date->format('Y-m-d'))->values();
                            $serviceIcon = $shopIcons[$service->icone ?? 'calendar'] ?? 'shop-star-icon';
                        @endphp
                        <article class="shop-service-card">
                            <span class="shop-service-icon"><svg aria-hidden="true"><use href="#{{ $serviceIcon }}"/></svg></span>
                            <h3>{{ $service->nom }}</h3>
                            <p>{{ $service->description ?: 'Contactez l’établissement pour en savoir plus.' }}</p>
                            <button class="shop-button shop-button-outline shop-service-reserve" type="button" data-service-id="{{ $service->id }}" data-service-name="{{ $service->nom }}" data-disabled-dates="{{ $serviceDates->toJson() }}">Réserver ce service</button>
                        </article>
                    @empty
                        <p class="shop-contact-block">Aucun service disponible actuellement.</p>
                    @endforelse
                </div>
            </section>

            <section class="shop-section shop-catalog" id="produits" aria-labelledby="shop-products-title">
                <div class="shop-section-heading">
                    <div><h2 id="shop-products-title">Le catalogue</h2><p>Disponibilité calculée à partir du stock et des demandes en cours.</p></div>
                </div>
                <div class="shop-catalog-controls">
                    <div class="shop-categories" id="shop-categories" aria-label="Filtrer par catégorie">
                        <button class="shop-chip is-active" type="button" data-category-filter="all" aria-pressed="true">Tout voir</button>
                        @foreach($categories as $category)
                            <button class="shop-chip" type="button" data-category-filter="{{ $category->id }}" aria-pressed="false">{{ $category->nom }}</button>
                        @endforeach
                    </div>
                    <div class="shop-filter-row">
                        <div class="shop-filter">
                            <label for="shop-availability">Disponibilité</label>
                            <select id="shop-availability"><option value="all">Toutes</option><option value="available">En stock</option><option value="low">Stock faible</option><option value="none">Indisponible</option></select>
                        </div>
                        <div class="shop-filter">
                            <label for="shop-sort">Trier par</label>
                            <select id="shop-sort"><option value="newest">Nouveautés</option><option value="name">Nom</option><option value="price-asc">Prix croissant</option><option value="price-desc">Prix décroissant</option></select>
                        </div>
                        <div class="shop-filter shop-filter-price">
                            <label>Fourchette de prix (CDF)</label>
                            <div class="shop-price-range"><input id="shop-price-min" type="number" min="0" inputmode="decimal" placeholder="Minimum" aria-label="Prix minimum en CDF"><input id="shop-price-max" type="number" min="0" inputmode="decimal" placeholder="Maximum" aria-label="Prix maximum en CDF"></div>
                        </div>
                        <div class="shop-currency" role="group" aria-label="Devise d’affichage">
                            <button class="is-active" type="button" data-currency="CDF" aria-pressed="true">CDF</button><button type="button" data-currency="USD" aria-pressed="false">USD</button>
                        </div>
                    </div>
                </div>
                <p class="shop-result-count" id="shop-result-count" aria-live="polite"></p>
                <div class="shop-product-grid" id="shop-product-grid">
                    @foreach($products as $product)
                        @php
                            $available = (int) $product->available_quantity;
                            $minimumStock = (int) ($product->stock->minimum_stock ?? 0);
                            $priceCdf = (float) ($product->stock->selling_price ?? 0);
                            $priceUsd = $usdCdfRate > 0 ? $priceCdf / $usdCdfRate : $priceCdf;
                            $categoryName = $product->category->nom ?? 'Autres';
                            $imageUrl = $product->image ? asset('storage/' . ltrim(str_replace('public/', '', $product->image), '/')) : '';
                        @endphp
                        <article class="shop-product-card" data-product-id="{{ $product->id }}" data-title="{{ $product->name }}" data-description="{{ $product->description }}" data-category="{{ $product->category_id }}" data-category-name="{{ $categoryName }}" data-price-cdf="{{ $priceCdf }}" data-price-usd="{{ $priceUsd }}" data-image="{{ $imageUrl }}" data-available="{{ $available }}" data-minimum="{{ $minimumStock }}" data-created="{{ $product->created_at?->timestamp ?? 0 }}" data-unit="{{ $product->unit->symbol ?? '' }}">
                            <button class="shop-product-preview" type="button" aria-label="Voir la fiche de {{ $product->name }}">
                                <span class="shop-product-image">
                                    @if($imageUrl)<img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy" onerror="this.remove()">@else<svg class="shop-product-placeholder" aria-hidden="true"><use href="#shop-box-icon"/></svg>@endif
                                    @if($available <= 0)<span class="shop-stock-badge shop-stock-none">Indisponible</span>@elseif($minimumStock > 0 && $available <= $minimumStock)<span class="shop-stock-badge shop-stock-low">Stock faible</span>@else<span class="shop-stock-badge">En stock</span>@endif
                                </span>
                                <span class="shop-product-copy">
                                    <span class="shop-product-category">{{ $categoryName }}</span>
                                    <span class="shop-product-title">{{ $product->name }}</span>
                                    <span class="shop-product-price"><span class="shop-cdf-price">{{ number_format($priceCdf, 0, ',', ' ') }} CDF</span><span class="shop-usd-price shop-card-hidden">{{ number_format($priceUsd, 2, ',', ' ') }} USD</span></span>
                                </span>
                            </button>
                            <div class="shop-product-actions"><button class="shop-button shop-button-primary shop-add-product" type="button" {{ $available <= 0 ? 'disabled' : '' }}>Réserver</button></div>
                        </article>
                    @endforeach
                    @if($products->isEmpty())<div class="shop-empty"><h3>Catalogue en préparation</h3><p>Aucun produit actif n’est publié pour le moment.</p></div>@endif
                </div>
                <div class="shop-more"><button class="shop-button shop-button-outline" type="button" id="shop-show-more" hidden>Voir plus de produits</button></div>
            </section>

            <section class="shop-section" aria-labelledby="shop-gallery-title">
                <div class="shop-section-heading"><div><h2 id="shop-gallery-title">Galerie</h2><p>Quelques images de l’établissement.</p></div></div>
                @if($etablissement->photos->isNotEmpty())
                    <div class="shop-gallery">
                        @foreach($etablissement->photos as $photo)
                            <button class="shop-gallery-button" type="button" data-gallery-index="{{ $loop->index }}" aria-label="Agrandir {{ $photo->titre ?: 'la photo' }}"><img src="{{ asset('storage/' . str_replace('public/', '', $photo->image_path)) }}" alt="{{ $photo->titre ?: 'Photo de ' . $etablissement->nom }}" loading="lazy"></button>
                        @endforeach
                    </div>
                @else
                    <p class="shop-contact-block">Aucune photo disponible.</p>
                @endif
            </section>

            <section class="shop-section" id="contact" aria-labelledby="shop-contact-title">
                <div class="shop-section-heading"><div><h2 id="shop-contact-title">Nous trouver</h2><p>Contactez l’établissement pour toute information.</p></div></div>
                <div class="shop-contact-grid">
                    <div class="shop-contact-block">
                        <h3>Coordonnées</h3>
                        @if($etablissement->telephone)<p><a href="tel:{{ $etablissement->telephone }}">{{ $etablissement->telephone }}</a></p>@endif
                        @if($etablissement->email)<p><a href="mailto:{{ $etablissement->email }}">{{ $etablissement->email }}</a></p>@endif
                        @if($etablissement->website)<p><a href="{{ $etablissement->website }}" target="_blank" rel="noopener">{{ $etablissement->website }}</a></p>@endif
                        @if($etablissement->facebook || $etablissement->twitter || $etablissement->instagram)
                            <div class="shop-socials">
                                @if($etablissement->facebook)<a href="{{ $etablissement->facebook }}" target="_blank" rel="noopener">Facebook</a>@endif
                                @if($etablissement->twitter)<a href="{{ $etablissement->twitter }}" target="_blank" rel="noopener">X / Twitter</a>@endif
                                @if($etablissement->instagram)<a href="{{ $etablissement->instagram }}" target="_blank" rel="noopener">Instagram</a>@endif
                            </div>
                        @endif
                    </div>
                    <div class="shop-contact-block">
                        <h3>Adresse et horaires</h3>
                        <p>{{ collect([$etablissement->avenue ? 'Av. ' . $etablissement->avenue : null, $etablissement->numero ? 'N° ' . $etablissement->numero : null, $etablissement->commune, $etablissement->ville])->filter()->implode(', ') ?: 'Adresse non renseignée' }}</p>
                        <p>Horaires non renseignés</p>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <footer class="shop-footer">
        <div class="shop-shell shop-footer-inner">
            <p><strong>{{ $etablissement->nom }}</strong><br>{{ $etablissement->telephone ?: 'Téléphone non renseigné' }}<br>{{ $etablissement->email ?: 'Adresse e-mail non renseignée' }}</p>
            <div class="shop-footer-links"><a href="#services">Services</a><a href="#produits">Produits</a><a href="#contact">Contact</a><a href="{{ route('contact') }}">Bisika</a></div>
        </div>
    </footer>

    <dialog class="shop-dialog" id="shop-product-dialog" aria-labelledby="shop-detail-title">
        <div class="shop-dialog-head"><h2 id="shop-detail-title">Fiche produit</h2><button class="shop-close" type="button" data-close-dialog="shop-product-dialog" aria-label="Fermer">×</button></div>
        <div class="shop-dialog-body">
            <img class="shop-detail-image" id="shop-detail-image" alt="" hidden>
            <p class="shop-eyebrow" id="shop-detail-category"></p>
            <p class="shop-product-price" id="shop-detail-price"></p>
            <p class="shop-detail-description" id="shop-detail-description"></p>
            <p id="shop-detail-availability"></p>
            <button class="shop-button shop-button-primary" type="button" id="shop-detail-add">Ajouter au panier</button>
            @if($etablissement->whatsapp_number)
                <a class="shop-button shop-detail-whatsapp" id="shop-detail-whatsapp" href="https://wa.me/{{ $etablissement->whatsapp_number }}?text={{ rawurlencode('Bonjour, je suis intéressé par le produit. Est-il disponible ?') }}" target="_blank" rel="noopener">Demander sur WhatsApp</a>
            @endif
        </div>
    </dialog>

    @if($etablissement->whatsapp_number)
        <a class="shop-whatsapp-fab" id="shop-whatsapp-fab" href="https://wa.me/{{ $etablissement->whatsapp_number }}?text={{ rawurlencode($whatsappWelcomeMessage) }}" target="_blank" rel="noopener" aria-label="Discuter avec {{ $etablissement->nom }} sur WhatsApp" title="Discuter sur WhatsApp">
            <svg viewBox="0 0 32 32" aria-hidden="true"><path d="M16.04 3C8.86 3 3.02 8.82 3.02 15.98c0 2.29.6 4.53 1.74 6.51L3 29l6.69-1.74a13.1 13.1 0 0 0 6.34 1.62h.01c7.17 0 13.01-5.82 13.01-12.98A12.87 12.87 0 0 0 25.22 6.7 12.95 12.95 0 0 0 16.04 3Zm0 23.68h-.01c-1.98 0-3.92-.53-5.62-1.54l-.4-.24-3.97 1.03 1.06-3.86-.26-.4a10.74 10.74 0 0 1-1.65-5.69c0-5.98 4.88-10.84 10.87-10.84a10.8 10.8 0 0 1 7.7 3.18 10.76 10.76 0 0 1 3.18 7.68c0 5.98-4.88 10.84-10.9 10.84Zm5.97-8.12c-.33-.16-1.94-.96-2.24-1.07-.3-.11-.52-.16-.73.16-.22.32-.84 1.07-1.03 1.29-.19.22-.38.24-.71.08-.33-.16-1.38-.51-2.63-1.62-.97-.86-1.63-1.92-1.82-2.25-.19-.32-.02-.5.14-.66.15-.14.33-.38.49-.57.16-.19.22-.32.33-.54.11-.21.06-.4-.03-.56-.08-.16-.73-1.75-1-2.4-.26-.63-.53-.54-.73-.55h-.62c-.21 0-.56.08-.86.4-.3.32-1.13 1.1-1.13 2.69 0 1.59 1.16 3.13 1.32 3.35.16.22 2.28 3.47 5.52 4.86.77.33 1.37.53 1.84.68.77.24 1.47.21 2.02.13.62-.09 1.94-.79 2.21-1.56.27-.77.27-1.43.19-1.56-.08-.14-.3-.22-.62-.38Z"/></svg>
        </a>
    @endif

    <div class="shop-cart-layer" id="shop-cart-layer" aria-hidden="true">
        <aside class="shop-cart-panel" id="shop-cart-panel" role="dialog" aria-modal="true" aria-labelledby="shop-cart-title" tabindex="-1">
            <div class="shop-dialog-head"><h2 id="shop-cart-title">Réservation</h2><button class="shop-close" type="button" id="shop-cart-close" aria-label="Fermer le panier">×</button></div>
            <div class="shop-cart-items" id="shop-cart-items" aria-live="polite"></div>
            <form class="shop-cart-form" action="{{ route('reservations.articles.store') }}" method="POST" id="shop-cart-form">
                @csrf
                <input type="hidden" name="etablissement_id" value="{{ $etablissement->id }}">
                <div id="shop-cart-hidden-fields"></div>
                <label for="shop-client-name">Nom complet</label><input id="shop-client-name" name="client_name" value="{{ old('client_name') }}" required maxlength="100" autocomplete="name">
                @error('client_name')<p class="shop-form-error">{{ $message }}</p>@enderror
                <label for="shop-client-phone">Téléphone</label><input id="shop-client-phone" name="client_phone" type="tel" value="{{ old('client_phone') }}" required maxlength="25" autocomplete="tel">
                @error('client_phone')<p class="shop-form-error">{{ $message }}</p>@enderror
                <label for="shop-client-message">Message (facultatif)</label><textarea id="shop-client-message" name="message" maxlength="1000">{{ old('message') }}</textarea>
                @error('product_ids')<p class="shop-form-error">{{ $message }}</p>@enderror
                @error('quantities')<p class="shop-form-error">{{ $message }}</p>@enderror
                @error('message')<p class="shop-form-error">{{ $message }}</p>@enderror
                <button class="shop-button shop-button-primary" id="shop-submit-reservation" type="submit">Envoyer la demande</button>
            </form>
        </aside>
    </div>

    <dialog class="shop-dialog" id="shop-service-dialog" aria-labelledby="shop-service-dialog-title">
        <div class="shop-dialog-head"><h2 id="shop-service-dialog-title">Réservation de service</h2><button class="shop-close" type="button" data-close-dialog="shop-service-dialog" aria-label="Fermer">×</button></div>
        <form class="shop-service-form" id="shop-service-form">
            <input type="hidden" id="shop-service-id" name="service_id">
            <label for="shop-service-date">Date souhaitée</label><input id="shop-service-date" type="date" required>
            <label for="shop-service-client">Nom complet</label><input id="shop-service-client" type="text" required maxlength="100" autocomplete="name">
            <label for="shop-service-phone">Téléphone</label><input id="shop-service-phone" type="tel" required maxlength="25" autocomplete="tel">
            <p class="shop-live-message" id="shop-service-message" role="status"></p>
            <button class="shop-button shop-button-primary" type="submit">Confirmer la réservation</button>
        </form>
    </dialog>

    <dialog class="shop-dialog" id="shop-gallery-dialog" aria-labelledby="shop-gallery-dialog-title">
        <div class="shop-dialog-head"><h2 id="shop-gallery-dialog-title">Galerie</h2><button class="shop-close" type="button" data-close-dialog="shop-gallery-dialog" aria-label="Fermer">×</button></div>
        <div class="shop-dialog-body"><img class="shop-detail-image" id="shop-gallery-image" alt=""><div class="shop-hero-actions"><button class="shop-button shop-button-outline" type="button" id="shop-gallery-prev">Précédente</button><button class="shop-button shop-button-outline" type="button" id="shop-gallery-next">Suivante</button></div></div>
    </dialog>

    <script>
        const productCards = [...document.querySelectorAll('.shop-product-card[data-product-id]')];
        const categoryButtons = [...document.querySelectorAll('[data-category-filter]')];
        const searchInput = document.getElementById('shop-search');
        const suggestions = document.getElementById('shop-suggestions');
        const cart = new Map();
        const pageSize = 12;
        let visibleLimit = pageSize;
        let activeCategory = 'all';
        let currentCurrency = 'CDF';
        let detailCard = null;
        let galleryIndex = 0;
        const whatsappNumber = @json($etablissement->whatsapp_number);
        const whatsappWelcomeMessage = @json($whatsappWelcomeMessage);
        const galleryData = @json($galleryPhotos);

        const normalized = value => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('fr');
        const stockStatus = card => Number(card.dataset.available) <= 0 ? 'none' : Number(card.dataset.minimum) > 0 && Number(card.dataset.available) <= Number(card.dataset.minimum) ? 'low' : 'available';
        const productInfo = card => ({
            id: Number(card.dataset.productId), name: card.dataset.title, price: Number(card.dataset.priceCdf),
            available: Number(card.dataset.available), unit: card.dataset.unit || '', image: card.dataset.image
        });
        const formatWhatsAppPrice = card => currentCurrency === 'CDF'
            ? `${new Intl.NumberFormat('fr-FR').format(Number(card.dataset.priceCdf))} CDF`
            : `${new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 2 }).format(Number(card.dataset.priceUsd))} USD`;
        const setWhatsAppMessage = (link, message) => {
            if (link && whatsappNumber) link.href = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;
        };
        setWhatsAppMessage(document.getElementById('shop-whatsapp-fab'), whatsappWelcomeMessage);

        function matchingCards() {
            const query = normalized(searchInput.value.trim());
            const availability = document.getElementById('shop-availability').value;
            const minimum = Number(document.getElementById('shop-price-min').value) || 0;
            const maximumValue = document.getElementById('shop-price-max').value;
            const maximum = maximumValue === '' ? Infinity : Number(maximumValue);
            return productCards.filter(card => {
                const searchText = normalized(`${card.dataset.title} ${card.dataset.categoryName}`);
                const status = stockStatus(card);
                return (!query || searchText.includes(query))
                    && (activeCategory === 'all' || card.dataset.category === activeCategory)
                    && (availability === 'all' || status === availability)
                    && Number(card.dataset.priceCdf) >= minimum
                    && Number(card.dataset.priceCdf) <= maximum;
            }).sort((a, b) => {
                const mode = document.getElementById('shop-sort').value;
                if (mode === 'name') return a.dataset.title.localeCompare(b.dataset.title, 'fr');
                if (mode === 'price-asc') return Number(a.dataset.priceCdf) - Number(b.dataset.priceCdf);
                if (mode === 'price-desc') return Number(b.dataset.priceCdf) - Number(a.dataset.priceCdf);
                return Number(b.dataset.created) - Number(a.dataset.created);
            });
        }

        function renderResults() {
            const matches = matchingCards();
            matches.forEach((card, index) => {
                document.getElementById('shop-product-grid').appendChild(card);
                card.classList.toggle('shop-card-hidden', index >= visibleLimit);
            });
            productCards.filter(card => !matches.includes(card)).forEach(card => card.classList.add('shop-card-hidden'));
            document.getElementById('shop-result-count').textContent = `${matches.length} produit${matches.length === 1 ? '' : 's'}`;
            const moreButton = document.getElementById('shop-show-more');
            moreButton.hidden = matches.length <= visibleLimit;
            let empty = document.getElementById('shop-empty-results');
            if (matches.length === 0 && productCards.length > 0) {
                if (!empty) {
                    empty = document.createElement('div');
                    empty.id = 'shop-empty-results';
                    empty.className = 'shop-empty';
                    const title = document.createElement('h3'); title.textContent = 'Aucun résultat';
                    const message = document.createElement('p'); message.textContent = 'Essayez une catégorie parmi celles disponibles.';
                    const categoryList = document.createElement('div'); categoryList.className = 'shop-empty-categories';
                    categoryButtons.slice(1).forEach(button => {
                        const suggestion = document.createElement('button'); suggestion.type = 'button'; suggestion.className = 'shop-chip'; suggestion.textContent = button.textContent;
                        suggestion.addEventListener('click', () => button.click()); categoryList.appendChild(suggestion);
                    });
                    empty.append(title, message, categoryList);
                    document.getElementById('shop-product-grid').appendChild(empty);
                }
                empty.hidden = false;
            } else if (empty) empty.hidden = true;
        }

        function renderSuggestions() {
            const query = normalized(searchInput.value.trim());
            suggestions.replaceChildren();
            if (!query) { suggestions.classList.remove('is-open'); return; }
            const matches = productCards.filter(card => normalized(`${card.dataset.title} ${card.dataset.categoryName}`).includes(query)).slice(0, 5);
            if (matches.length === 0) { suggestions.classList.remove('is-open'); return; }
            matches.forEach(card => {
                const button = document.createElement('button'); button.type = 'button'; button.className = 'shop-suggestion'; button.setAttribute('role', 'option');
                const name = document.createElement('span'); name.textContent = card.dataset.title;
                const category = document.createElement('small'); category.textContent = card.dataset.categoryName;
                button.append(name, category);
                button.addEventListener('click', () => { searchInput.value = card.dataset.title; suggestions.classList.remove('is-open'); visibleLimit = pageSize; renderResults(); document.getElementById('produits').scrollIntoView({ behavior: 'smooth' }); });
                suggestions.appendChild(button);
            });
            suggestions.classList.add('is-open');
        }

        function addToCart(card) {
            const item = productInfo(card);
            if (item.available < 1) return;
            const current = cart.get(item.id);
            cart.set(item.id, { ...item, quantity: Math.min(item.available, (current?.quantity || 0) + 1) });
            window.BisikaAnalytics?.track('add_to_cart', { item_name: item.name, quantity: 1 });
            renderCart();
            openCart();
        }

        function renderCart() {
            const container = document.getElementById('shop-cart-items');
            const count = [...cart.values()].reduce((total, item) => total + item.quantity, 0);
            document.getElementById('shop-cart-count').textContent = count;
            container.replaceChildren();
            document.getElementById('shop-cart-hidden-fields').replaceChildren();
            if (cart.size === 0) {
                const message = document.createElement('p'); message.className = 'shop-detail-description'; message.textContent = 'Votre panier est vide.'; container.appendChild(message);
                document.getElementById('shop-submit-reservation').disabled = true;
                return;
            }
            document.getElementById('shop-submit-reservation').disabled = false;
            cart.forEach(item => {
                const row = document.createElement('div'); row.className = 'shop-cart-line';
                const copy = document.createElement('div');
                const title = document.createElement('h3'); title.textContent = item.name;
                const price = document.createElement('p'); price.textContent = `${new Intl.NumberFormat('fr-FR').format(item.price)} CDF`;
                copy.append(title, price);
                const controls = document.createElement('div'); controls.className = 'shop-quantity';
                const minus = document.createElement('button'); minus.type = 'button'; minus.textContent = '−'; minus.setAttribute('aria-label', `Réduire la quantité de ${item.name}`);
                const quantity = document.createElement('span'); quantity.textContent = item.quantity;
                const plus = document.createElement('button'); plus.type = 'button'; plus.textContent = '+'; plus.setAttribute('aria-label', `Augmenter la quantité de ${item.name}`); plus.disabled = item.quantity >= item.available;
                minus.addEventListener('click', () => { if (item.quantity <= 1) cart.delete(item.id); else item.quantity--; renderCart(); });
                plus.addEventListener('click', () => { if (item.quantity < item.available) item.quantity++; renderCart(); });
                controls.append(minus, quantity, plus); row.append(copy, controls); container.appendChild(row);
                const productId = document.createElement('input'); productId.type = 'hidden'; productId.name = 'product_ids[]'; productId.value = item.id;
                const itemQuantity = document.createElement('input'); itemQuantity.type = 'hidden'; itemQuantity.name = `quantities[${item.id}]`; itemQuantity.value = item.quantity;
                document.getElementById('shop-cart-hidden-fields').append(productId, itemQuantity);
            });
        }

        function openCart() {
            const layer = document.getElementById('shop-cart-layer');
            layer.classList.add('is-open'); layer.setAttribute('aria-hidden', 'false'); document.body.style.overflow = 'hidden';
            document.getElementById('shop-cart-panel').focus();
        }
        function closeCart() {
            const layer = document.getElementById('shop-cart-layer');
            layer.classList.remove('is-open'); layer.setAttribute('aria-hidden', 'true'); document.body.style.overflow = '';
            document.getElementById('shop-cart-open').focus();
        }

        productCards.forEach(card => {
            card.querySelector('.shop-product-preview').addEventListener('click', () => {
                detailCard = card;
                window.BisikaAnalytics?.track('view_item', {
                    item_name: card.dataset.title,
                    price: Number(currentCurrency === 'CDF' ? card.dataset.priceCdf : card.dataset.priceUsd),
                    currency: currentCurrency,
                });
                document.getElementById('shop-detail-title').textContent = card.dataset.title;
                document.getElementById('shop-detail-category').textContent = card.dataset.categoryName;
                document.getElementById('shop-detail-price').textContent = currentCurrency === 'CDF' ? `${new Intl.NumberFormat('fr-FR').format(Number(card.dataset.priceCdf))} CDF` : `${new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 2 }).format(Number(card.dataset.priceUsd))} USD`;
                document.getElementById('shop-detail-description').textContent = card.dataset.description || 'Aucune description disponible.';
                const image = document.getElementById('shop-detail-image'); image.hidden = !card.dataset.image; image.src = card.dataset.image; image.alt = card.dataset.title;
                const available = Number(card.dataset.available); const status = stockStatus(card);
                document.getElementById('shop-detail-availability').textContent = status === 'none' ? 'Indisponible' : status === 'low' ? `Stock faible · ${available} disponible${available > 1 ? 's' : ''}` : `En stock · ${available} disponible${available > 1 ? 's' : ''}`;
                setWhatsAppMessage(
                    document.getElementById('shop-detail-whatsapp'),
                    `Bonjour, je suis intéressé par ${card.dataset.title} (${formatWhatsAppPrice(card)}). Est-il disponible ?`
                );
                document.getElementById('shop-detail-add').disabled = available < 1;
                document.getElementById('shop-product-dialog').showModal();
            });
            card.querySelector('.shop-add-product').addEventListener('click', () => addToCart(card));
        });

        categoryButtons.forEach(button => button.addEventListener('click', () => {
            activeCategory = button.dataset.categoryFilter; visibleLimit = pageSize;
            categoryButtons.forEach(chip => { const active = chip === button; chip.classList.toggle('is-active', active); chip.setAttribute('aria-pressed', String(active)); });
            renderResults();
        }));
        searchInput.addEventListener('input', () => { visibleLimit = pageSize; renderSuggestions(); renderResults(); });
        document.addEventListener('click', event => { if (!event.target.closest('.shop-search-wrap')) suggestions.classList.remove('is-open'); });
        ['shop-availability', 'shop-sort', 'shop-price-min', 'shop-price-max'].forEach(id => document.getElementById(id).addEventListener('input', renderResults));
        document.getElementById('shop-show-more').addEventListener('click', () => { visibleLimit += pageSize; renderResults(); });
        document.querySelectorAll('[data-currency]').forEach(button => button.addEventListener('click', () => {
            currentCurrency = button.dataset.currency;
            document.querySelectorAll('[data-currency]').forEach(currencyButton => { const active = currencyButton === button; currencyButton.classList.toggle('is-active', active); currencyButton.setAttribute('aria-pressed', String(active)); });
            document.querySelectorAll('.shop-cdf-price').forEach(price => price.classList.toggle('shop-card-hidden', currentCurrency !== 'CDF'));
            document.querySelectorAll('.shop-usd-price').forEach(price => price.classList.toggle('shop-card-hidden', currentCurrency !== 'USD'));
            if (detailCard) {
                document.getElementById('shop-detail-price').textContent = formatWhatsAppPrice(detailCard);
                setWhatsAppMessage(document.getElementById('shop-detail-whatsapp'), `Bonjour, je suis intéressé par ${detailCard.dataset.title} (${formatWhatsAppPrice(detailCard)}). Est-il disponible ?`);
            }
        }));

        document.getElementById('shop-cart-open').addEventListener('click', openCart);
        document.getElementById('shop-cart-close').addEventListener('click', closeCart);
        document.getElementById('shop-cart-layer').addEventListener('click', event => { if (event.target.id === 'shop-cart-layer') closeCart(); });
        document.getElementById('shop-detail-add').addEventListener('click', () => { if (detailCard) { document.getElementById('shop-product-dialog').close(); addToCart(detailCard); } });
        document.getElementById('shop-hero-reserve').addEventListener('click', () => {
            document.getElementById('produits').scrollIntoView({ behavior: 'smooth' });
            if (cart.size) openCart();
            else document.querySelector('.shop-add-product:not(:disabled)')?.focus({ preventScroll: true });
        });
        document.querySelectorAll('[data-close-dialog]').forEach(button => button.addEventListener('click', () => document.getElementById(button.dataset.closeDialog).close()));

        document.getElementById('shop-menu-toggle').addEventListener('click', event => {
            const nav = document.getElementById('shop-navigation'); const open = nav.classList.toggle('is-open');
            event.currentTarget.setAttribute('aria-expanded', String(open)); event.currentTarget.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
        });
        document.querySelectorAll('.shop-nav a').forEach(link => link.addEventListener('click', () => {
            document.getElementById('shop-navigation').classList.remove('is-open');
            const menuButton = document.getElementById('shop-menu-toggle');
            menuButton.setAttribute('aria-expanded', 'false');
            menuButton.setAttribute('aria-label', 'Ouvrir le menu');
        }));

        document.querySelectorAll('.shop-service-reserve').forEach(button => button.addEventListener('click', () => {
            document.getElementById('shop-service-dialog-title').textContent = `Réserver · ${button.dataset.serviceName}`;
            document.getElementById('shop-service-id').value = button.dataset.serviceId;
            document.getElementById('shop-service-date').value = '';
            document.getElementById('shop-service-date').min = new Date().toISOString().slice(0, 10);
            document.getElementById('shop-service-client').value = '';
            document.getElementById('shop-service-phone').value = '';
            document.getElementById('shop-service-message').textContent = '';
            document.querySelector('#shop-service-form [type="submit"]').disabled = false;
            document.getElementById('shop-service-dialog').showModal();
        }));
        document.getElementById('shop-service-form').addEventListener('submit', async event => {
            event.preventDefault();
            const message = document.getElementById('shop-service-message');
            const date = document.getElementById('shop-service-date').value;
            const serviceButton = [...document.querySelectorAll('.shop-service-reserve')].find(button => button.dataset.serviceId === document.getElementById('shop-service-id').value);
            if (JSON.parse(serviceButton?.dataset.disabledDates || '[]').includes(date)) { message.textContent = 'Cette date est déjà réservée. Choisissez-en une autre.'; return; }
            try {
                const response = await fetch('{{ route('reservations.store') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: JSON.stringify({ service_id: document.getElementById('shop-service-id').value, date, client_name: document.getElementById('shop-service-client').value.trim(), client_phone: document.getElementById('shop-service-phone').value.trim() }) });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || 'La réservation n’a pas pu être envoyée.');
                window.BisikaAnalytics?.track('reservation_submit');
                message.textContent = data.message || 'Votre demande a bien été envoyée.';
                event.currentTarget.querySelector('[type="submit"]').disabled = true;
            } catch (error) { message.textContent = error.message; }
        });

        document.querySelectorAll('[data-gallery-index]').forEach(button => button.addEventListener('click', () => {
            galleryIndex = Number(button.dataset.galleryIndex); updateGallery(); document.getElementById('shop-gallery-dialog').showModal();
        }));
        function updateGallery() {
            if (!galleryData.length) return;
            const photo = galleryData[galleryIndex]; const image = document.getElementById('shop-gallery-image'); image.src = photo.src; image.alt = photo.title || 'Photo de l’établissement';
            document.getElementById('shop-gallery-dialog-title').textContent = photo.title || 'Galerie';
        }
        document.getElementById('shop-gallery-prev').addEventListener('click', () => { galleryIndex = (galleryIndex - 1 + galleryData.length) % galleryData.length; updateGallery(); });
        document.getElementById('shop-gallery-next').addEventListener('click', () => { galleryIndex = (galleryIndex + 1) % galleryData.length; updateGallery(); });
        document.addEventListener('keydown', event => { if (event.key === 'Escape' && document.getElementById('shop-cart-layer').classList.contains('is-open')) closeCart(); });

        const oldProductIds = @json(old('product_ids', []));
        const oldQuantities = @json(old('quantities', []));
        oldProductIds.forEach(id => {
            const card = productCards.find(productCard => Number(productCard.dataset.productId) === Number(id));
            if (card) { const item = productInfo(card); const quantity = Math.min(item.available, Number(oldQuantities[id]) || 1); if (quantity > 0) cart.set(item.id, { ...item, quantity }); }
        });
        renderCart(); renderResults();
        @if($errors->has('product_ids') || $errors->has('quantities') || $errors->has('client_name') || $errors->has('client_phone') || $errors->has('message'))openCart();@endif
    </script>

    @if(false)
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

    <!-- Legacy public profile markup retained for reference during migration. -->
    @endif
</body>
</html>
