@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
                @csrf

                <!-- Name -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="name" class="form-label fw-bold">{{ __('Full Name') }}</label>
                        <span class="text-muted small">{{ __('Required') }}</span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                               name="name" value="{{ old('name') }}" placeholder="John Doe" required autofocus autocomplete="name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Email Address -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="email" class="form-label fw-bold">{{ __('Email Address') }}</label>
                        <span class="text-muted small">{{ __('Required') }}</span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" placeholder="your@email.com" required autocomplete="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="password" class="form-label fw-bold">{{ __('Password') }}</label>
                        <span class="text-muted small">{{ __('Minimum 8 characters') }}</span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                               name="password" placeholder="••••••••" required autocomplete="new-password">
                        <button class="btn btn-outline-secondary toggle-password" type="button">
                            <i class="fas fa-eye"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="password-strength-meter mt-2 d-none">
                        <div class="d-flex justify-content-between small text-muted mb-1">
                            <span>{{ __('Password Strength') }}:</span>
                            <span class="strength-text">Weak</span>
                        </div>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar bg-danger strength-bar" role="progressbar" style="width: 25%"></div>
                        </div>
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label fw-bold">{{ __('Confirm Password') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input id="password_confirmation" type="password" class="form-control"
                               name="password_confirmation" placeholder="••••••••" required autocomplete="new-password">
                        <button class="btn btn-outline-secondary toggle-password" type="button">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" id="terms" name="terms" required>
                    <label class="form-check-label" for="terms">
                        {{ __('I agree to the') }} <a href="#" class="text-primary">{{ __('Terms of Service') }}</a> {{ __('and') }} <a href="#" class="text-primary">{{ __('Privacy Policy') }}</a>
                    </label>
                    @error('terms')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ route('login') }}" class="text-primary text-decoration-none">
                        {{ __('Already have an account?') }}
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        {{ __('Register') }} <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.closest('.input-group').querySelector('input');
            const icon = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    // Password strength meter
    const passwordInput = document.getElementById('password');
    const strengthMeter = document.querySelector('.password-strength-meter');
    const strengthBar = document.querySelector('.strength-bar');
    const strengthText = document.querySelector('.strength-text');

    if (passwordInput && strengthMeter) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            if (password.length > 0) {
                strengthMeter.classList.remove('d-none');
                const strength = calculatePasswordStrength(password);
                updateStrengthMeter(strength);
            } else {
                strengthMeter.classList.add('d-none');
            }
        });
    }

    function calculatePasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength += 1;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1;
        if (password.match(/\d/)) strength += 1;
        if (password.match(/[^a-zA-Z0-9]/)) strength += 1;
        return Math.min(strength, 4); // Max strength of 4
    }

    function updateStrengthMeter(strength) {
        const colors = ['bg-danger', 'bg-warning', 'bg-info', 'bg-success'];
        const texts = [__('Weak'), __('Fair'), __('Good'), __('Strong')];
        const widths = ['25%', '50%', '75%', '100%'];

        strengthBar.className = 'progress-bar strength-bar';
        strengthBar.classList.add(colors[strength - 1]);
        strengthBar.style.width = widths[strength - 1];
        strengthText.textContent = texts[strength - 1];
    }

    // Bootstrap 5 form validation
    (function () {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })
    })()
});
</script>
@endpush
@endsection
