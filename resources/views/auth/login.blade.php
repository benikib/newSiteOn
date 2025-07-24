@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column align-items-center justify-content-center min-vh-100 bg-light py-5">
        <div class="w-100" style="max-width: 420px;">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-primary text-white py-4">
                    <h1 class="h4 text-center mb-0">{{ __('Connexion') }}</h1>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-medium">{{ __('Adresse e-mail') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}" placeholder="votre@email.com"
                                    required autofocus autocomplete="email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-medium">{{ __('Mot de passe') }}</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" placeholder="Entrez votre mot de passe" required
                                    autocomplete="current-password">
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    aria-label="Afficher/masquer le mot de passe">
                                    {{-- <i class="fas fa-eye"></i>
                                    <span class="visually-hidden">Afficher le mot de passe</span> --}}
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Minimum 8 caractères avec des chiffres et lettres</small>
                        </div>

                        <!-- Script pour basculer la visibilité du mot de passe -->
                        {{-- <script>
                            document.querySelectorAll('.toggle-password').forEach(button => {
                                button.addEventListener('click', function() {
                                    const passwordInput = this.previousElementSibling;
                                    const icon = this.querySelector('i');

                                    if (passwordInput.type === 'password') {
                                        passwordInput.type = 'text';
                                        icon.classList.replace('fa-eye', 'fa-eye-slash');
                                        this.setAttribute('aria-label', 'Masquer le mot de passe');
                                    } else {
                                        passwordInput.type = 'password';
                                        icon.classList.replace('fa-eye-slash', 'fa-eye');
                                        this.setAttribute('aria-label', 'Afficher le mot de passe');
                                    }
                                });
                            });
                        </script> --}}

                        <!-- Remember Me & Forgot Password -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember_me" name="remember">
                                <label class="form-check-label" for="remember_me">{{ __(' Se souvenir de moi') }}</label>
                            </div>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-decoration-none text-primary">
                                    {{ __('Forgot password?') }}
                                </a>
                            @endif
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>{{ __('Connexion') }}
                        </button>

                        <!-- Social Login (Optional) -->
                        {{-- <div class="text-center mb-3 position-relative">
                            <hr class="my-4">
                            <span class="px-2 bg-white position-absolute top-50 start-50 translate-middle text-muted">
                                {{ __('Or continue with') }}
                            </span>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="#" class="btn btn-outline-secondary">
                                <i class="fab fa-google me-2"></i> Google
                            </a>
                            <a href="#" class="btn btn-outline-primary">
                                <i class="fab fa-facebook-f me-2"></i> Facebook
                            </a>
                        </div> --}}
                    </form>
                </div>
            </div>

            <!-- Register Link -->
            <div class="text-center mt-4">
                <p class="text-muted">{{ __("Don't have an account?") }}
                    <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-medium">
                        {{ __('Sign up') }}
                    </a>
                </p>
                {{-- <a href="{{ url('/') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('Back to Home') }}
                </a> --}}
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus email field
            const emailInput = document.getElementById('email');
            if (emailInput) emailInput.focus();

            // Toggle password visibility
            const togglePassword = document.querySelector('.toggle-password');
            if (togglePassword) {
                togglePassword.addEventListener('click', function() {
                    const password = document.getElementById('password');
                    const icon = this.querySelector('i');
                    if (password.type === 'password') {
                        password.type = 'text';
                        icon.classList.replace('fa-eye', 'fa-eye-slash');
                    } else {
                        password.type = 'password';
                        icon.classList.replace('fa-eye-slash', 'fa-eye');
                    }
                });
            }

            // Form validation
            const forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        });
    </script>
@endsection
