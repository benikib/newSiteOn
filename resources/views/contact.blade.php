@extends('layouts.app')


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
    </style>


@section('content')
    <section class="contact-section">
        <div class="container contact-container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="contact-card">
                        <div class="contact-header">
                            <h2>Contactez-nous</h2>
                            <p>Nous sommes à votre écoute pour toutes vos questions et demandes</p>
                        </div>

                        <div class="row g-0">
                            <div class="col-lg-7">
                                <div class="contact-body">
                                    <div class="contact-info">
                                        <div class="contact-item">
                                            <div class="contact-icon">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </div>
                                            <div class="contact-details">
                                                <h4>Adresse</h4>
                                                <p>123 Avenue des Entreprises, Kinshasa, RDC</p>
                                            </div>
                                        </div>

                                        <div class="contact-item">
                                            <div class="contact-icon">
                                                <i class="fas fa-phone-alt"></i>
                                            </div>
                                            <div class="contact-details">
                                                <h4>Téléphone</h4>
                                                <p>+243 81 234 5678</p>
                                                <p>+243 89 876 5432</p>
                                            </div>
                                        </div>

                                        <div class="contact-item">
                                            <div class="contact-icon">
                                                <i class="fas fa-envelope"></i>
                                            </div>
                                            <div class="contact-details">
                                                <h4>Email</h4>
                                                <p>contact@bisika.cd</p>
                                                <p>support@bisika.cd</p>
                                            </div>
                                        </div>
                                    </div>

                                    <form class="contact-form" action="{{ route('contact.submit') }}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="name"
                                                    placeholder="Votre nom" required>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="email" class="form-control" name="email"
                                                    placeholder="Votre email" required>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control" name="subject" placeholder="Sujet">
                                        <textarea class="form-control" name="message" placeholder="Votre message" required></textarea>
                                        <button type="submit" class="submit-btn">
                                            <span>Envoyer le message</span>
                                        </button>
                                    </form>

                                    <div class="social-links">
                                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                                        <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                                        <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-5 d-none d-lg-block">
                                <div class="map-container h-100">
                                    <iframe
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3987.490476079041!2d15.29791181475798!3d-4.322879997000365!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1a6a3393e2b5e1d5%3A0x5a5a1e1e1e1e1e1e!2sKinshasa%2C%20R%C3%A9publique%20D%C3%A9mocratique%20du%20Congo!5e0!3m2!1sfr!2sfr!4v1620000000000!5m2!1sfr!2sfr"
                                        allowfullscreen="" loading="lazy"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animation du formulaire
            const formElements = document.querySelectorAll('.contact-form .form-control');

            formElements.forEach((element, index) => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                element.style.transition = `all 0.5s ease ${index * 0.1}s`;

                setTimeout(() => {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, 500 + (index * 100));
            });

            // Animation du bouton submit
            const submitBtn = document.querySelector('.submit-btn');
            submitBtn.style.opacity = '0';
            submitBtn.style.transform = 'translateY(20px)';
            submitBtn.style.transition = 'all 0.5s ease 0.6s';

            setTimeout(() => {
                submitBtn.style.opacity = '1';
                submitBtn.style.transform = 'translateY(0)';
            }, 1100);
        });
    </script>
@endsection
