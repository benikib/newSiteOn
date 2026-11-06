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

                <div class="mb-3">
                    <label class="form-label fw-bold" for="type_compte">Type de compte</label>
                    <select class="form-select @error('type_compte') is-invalid @enderror" id="type_compte" name="type_compte">
                        <option value="client" @selected(old('type_compte', 'client') === 'client')>Client</option>
                        <option value="entite_formalisee" @selected(old('type_compte') === 'entite_formalisee')>Entité formalisée</option>
                        <option value="entite_non_formalisee" @selected(old('type_compte') === 'entite_non_formalisee')>Entité non formalisée</option>
                        <option value="organisation_patronale" @selected(old('type_compte') === 'organisation_patronale')>Organisation patronale</option>
                    </select>
                    @error('type_compte')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">L’identifiant BISIKA du compte sera généré automatiquement.</small>
                </div>

                <div id="entity-registration-fields" class="border rounded p-3 mb-3 d-none">
                    <h2 class="h6 mb-3">Informations sur la structure</h2>
                    <div class="mb-3">
                        <label class="form-label" for="nom_entite">Nom de la structure</label>
                        <input class="form-control @error('nom_entite') is-invalid @enderror" id="nom_entite"
                            name="nom_entite" value="{{ old('nom_entite') }}" maxlength="255">
                        @error('nom_entite')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3" id="activity-type-field">
                        <label class="form-label" for="type_etablissement_id">Domaine d’activité</label>
                        <select class="form-select @error('type_etablissement_id') is-invalid @enderror" id="type_etablissement_id" name="type_etablissement_id">
                            <option value="">Choisir un domaine</option>
                            @foreach ($typesEtablissement as $type)
                                <option value="{{ $type->id }}" @selected(old('type_etablissement_id') == $type->id)>{{ $type->nom }}</option>
                            @endforeach
                        </select>
                        @error('type_etablissement_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="telephone">Téléphone</label>
                        <input class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone"
                            value="{{ old('telephone') }}" maxlength="25">
                        @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row">
                        @foreach (['ville' => 'Ville', 'commune' => 'Commune', 'quartier' => 'Quartier', 'avenue' => 'Avenue', 'numero' => 'Numéro'] as $field => $label)
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                                <input class="form-control @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}"
                                    value="{{ old($field) }}" maxlength="255">
                                @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        @endforeach
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
    const accountType = document.getElementById('type_compte');
    const entityFields = document.getElementById('entity-registration-fields');
    const activityTypeField = document.getElementById('activity-type-field');
    const entityName = document.getElementById('nom_entite');
    const entityPhone = document.getElementById('telephone');
    const activityType = document.getElementById('type_etablissement_id');

    const updateEntityFields = () => {
        const type = accountType.value;
        const isEntity = type !== 'client';
        const hasActivity = type === 'entite_formalisee' || type === 'entite_non_formalisee';
        entityFields.classList.toggle('d-none', !isEntity);
        activityTypeField.classList.toggle('d-none', !hasActivity);
        entityName.required = isEntity;
        entityPhone.required = isEntity;
        activityType.required = hasActivity;
    };
    accountType.addEventListener('change', updateEntityFields);
    updateEntityFields();

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
