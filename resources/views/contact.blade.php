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

        /* Section Contact */
        .contact-section {
            padding: 5rem 0;
            background: url('https://images.unsplash.com/photo-1517502884422-41eaead166d4?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center center;
            background-size: cover;
            position: relative;
        }

        .contact-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(26, 26, 46, 0.85);
            z-index: 0;
        }

        .contact-container {
            position: relative;
            z-index: 1;
        }

        .contact-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .contact-header {
            background: var(--gradient-primary);
            padding: 2.5rem;
            color: white;
            text-align: center;
        }

        .contact-header h2 {
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 2.2rem;
        }

        .contact-header p {
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .contact-body {
            padding: 2.5rem;
        }

        .contact-info {
            margin-bottom: 2.5rem;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .contact-icon {
            background: var(--gradient-primary);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 1.5rem;
            flex-shrink: 0;
        }

        .contact-details h4 {
            font-weight: 600;
            margin-bottom: 0.3rem;
            color: var(--dark-color);
        }

        .contact-details p {
            color: #6c757d;
            margin-bottom: 0;
        }

        .contact-form .form-control {
            border: 2px solid #e2e8f0;
            padding: 0.9rem 1.25rem;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-size: 1.05rem;
            margin-bottom: 1.5rem;
        }

        .contact-form .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.15);
        }

        .contact-form textarea.form-control {
            min-height: 150px;
        }

        .submit-btn {
            background: var(--gradient-primary);
            border: none;
            padding: 1rem 2.5rem;
            border-radius: 12px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(67, 97, 238, 0.3);
        }

        .submit-btn::after {
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

        .submit-btn:hover::after {
            opacity: 1;
        }

        .submit-btn span {
            position: relative;
            z-index: 1;
        }

        /* Carte de localisation */
        .map-container {
            height: 100%;
            min-height: 300px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Réseaux sociaux */
        .social-links {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        .social-link {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: var(--gradient-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 0.75rem;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 8px 15px rgba(67, 97, 238, 0.3);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .contact-body {
                padding: 2rem;
            }

            .contact-header {
                padding: 2rem;
            }
        }

        @media (max-width: 768px) {
            .contact-section {
                padding: 3rem 0;
            }

            .contact-header h2 {
                font-size: 1.8rem;
            }

            .contact-body {
                padding: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .contact-item {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .contact-icon {
                margin-right: 0;
                margin-bottom: 1rem;
            }

            .submit-btn {
                width: 100%;
            }
        }

        body { background: #f5f6f1; font-family: "Segoe UI", sans-serif; }
        .contact-page { max-width: 1160px; margin: 0 auto; padding: 2rem 0 4rem; }
        .contact-hero { padding: clamp(1.5rem, 5vw, 3.25rem); border-radius: 1.2rem; background: #203b30; color: #fff; }
        .contact-hero p { max-width: 630px; margin: .75rem 0 0; color: #d6e3da; line-height: 1.7; }
        .contact-eyebrow { margin: 0; color: #b9d9c6 !important; font-size: .78rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
        .contact-hero h1 { margin: .7rem 0 0; font: 600 clamp(2rem, 4vw, 3.3rem)/1.08 Georgia, serif; color: white; }
        .contact-layout { display: grid; grid-template-columns: minmax(240px, .8fr) minmax(0, 1.2fr); gap: clamp(1.5rem, 5vw, 4rem); padding: clamp(1.5rem, 5vw, 3rem) clamp(.25rem, 2vw, 1.25rem); }
        .contact-details-title, .contact-form-title { margin: 0 0 .45rem; font: 600 1.6rem Georgia, serif; color: #202923; }
        .contact-section-intro { margin: 0 0 1.5rem; color: #697770; line-height: 1.65; }
        .contact-method { display: flex; align-items: flex-start; gap: .9rem; padding: 1rem 0; border-bottom: 1px solid #e1e7e1; }
        .contact-method-icon { flex: 0 0 44px; width: 44px; height: 44px; display: grid; place-items: center; border-radius: .8rem; background: #e6eee7; color: #245c48; }
        .contact-method h3 { margin: 0 0 .2rem; font-size: .9rem; font-weight: 700; }
        .contact-method p, .contact-method a { margin: 0; color: #58665e; text-decoration-color: #afbbb2; overflow-wrap: anywhere; }
        .contact-form { padding: clamp(1.25rem, 3vw, 2rem); border: 1px solid #e1e7e1; border-radius: 1rem; background: white; }
        .contact-form .form-control { min-height: 48px; margin: 0; padding: .75rem .9rem; border: 1px solid #d8e0d9; border-radius: .65rem; font: inherit; }
        .contact-form textarea.form-control { min-height: 145px; resize: vertical; }
        .contact-form .form-control:focus { border-color: #245c48; box-shadow: 0 0 0 .2rem rgba(36,92,72,.14); }
        .contact-field { margin-top: 1rem; }
        .contact-field label { display: block; margin-bottom: .4rem; color: #34433a; font-size: .88rem; font-weight: 700; }
        .contact-submit { min-height: 48px; margin-top: 1.1rem; padding: 0 1.25rem; border: 0; border-radius: .65rem; background: #c45c36; color: white; font-weight: 700; }
        .contact-submit:hover { background: #a94628; }
        @media (max-width: 767px) { .contact-layout { grid-template-columns: 1fr; gap: 2rem; } .contact-page { padding-top: .75rem; } }
    </style>
@endsection


@section('content')
    <main class="contact-page">
        <header class="contact-hero">
            <p class="contact-eyebrow">L’équipe BISIKA</p>
            <h1>Parlons de votre projet.</h1>
            <p>Une question sur la plateforme, un besoin d’accompagnement ou une suggestion ? Écrivez-nous, nous vous répondrons dès que possible.</p>
        </header>

        <div class="contact-layout">
            <section aria-labelledby="contact-details-title">
                <h2 class="contact-details-title" id="contact-details-title">Nos coordonnées</h2>
                <p class="contact-section-intro">Retrouvez-nous ou choisissez le moyen de contact qui vous convient.</p>
                <div class="contact-method">
                    <span class="contact-method-icon"><i class="fas fa-location-dot" aria-hidden="true"></i></span>
                    <div><h3>Localisation</h3><p>Kinshasa, République démocratique du Congo</p></div>
                </div>
                <div class="contact-method">
                    <span class="contact-method-icon"><i class="fas fa-phone" aria-hidden="true"></i></span>
                    <div><h3>Téléphone</h3><a href="tel:+243978002597">+243 978 002 597</a></div>
                </div>
                <div class="contact-method">
                    <span class="contact-method-icon"><i class="fas fa-envelope" aria-hidden="true"></i></span>
                    <div><h3>E-mail</h3><a href="mailto:contactbisika@gmail.com">contactbisika@gmail.com</a></div>
                </div>
                <a class="btn btn-outline-success mt-4" href="https://maps.google.com/?q=Kinshasa%2C+RDC" target="_blank" rel="noopener">Voir Kinshasa sur la carte</a>
            </section>

            <section aria-labelledby="contact-form-title">
                <x-flash-alert />
                <form class="contact-form" action="{{ route('contact.submit') }}" method="POST">
                    @csrf
                    <h2 class="contact-form-title" id="contact-form-title">Envoyer un message</h2>
                    <p class="contact-section-intro">Les champs marqués sont nécessaires pour vous répondre.</p>
                    <div class="row g-3">
                        <div class="col-sm-6 contact-field">
                            <label for="contact-name">Votre nom</label>
                            <input id="contact-name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" autocomplete="name" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-sm-6 contact-field">
                            <label for="contact-email">Votre e-mail</label>
                            <input id="contact-email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="contact-field">
                        <label for="contact-subject">Sujet</label>
                        <input id="contact-subject" type="text" class="form-control @error('subject') is-invalid @enderror" name="subject" value="{{ old('subject') }}" maxlength="255">
                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="contact-field">
                        <label for="contact-message">Votre message</label>
                        <textarea id="contact-message" class="form-control @error('message') is-invalid @enderror" name="message" rows="6" required>{{ old('message') }}</textarea>
                        @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="contact-submit"><i class="fas fa-paper-plane me-2" aria-hidden="true"></i>Envoyer le message</button>
                </form>
            </section>
        </div>
    </main>
@endsection

@section('scripts')
@endsection
