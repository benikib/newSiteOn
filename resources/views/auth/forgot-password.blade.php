@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column align-items-center justify-content-center min-vh-100 bg-light py-5">
        <div class="w-100" style="max-width: 420px;">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-primary text-white py-4">
                    <h1 class="h4 text-center mb-0">{{ __('Mot de passe oublié') }}</h1>
                </div>

                <div class="card-body p-4">
                    <div class="mb-3 text-muted">
                        {{ __('Indiquez votre adresse e-mail et nous vous enverrons un lien pour réinitialiser votre mot de passe.') }}
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}" class="needs-validation" novalidate>
                        @csrf

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

                        <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                            <i class="fas fa-envelope-open-text me-2"></i>{{ __('Envoyer le lien de réinitialisation') }}
                        </button>

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-decoration-none text-primary">
                                <i class="fas fa-arrow-left me-1"></i> {{ __('Retour à la connexion') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            if (emailInput) emailInput.focus();

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
