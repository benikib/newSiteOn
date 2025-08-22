<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .password-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .password-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
        }

        .password-header {
            background: linear-gradient(120deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 25px;
            text-align: center;
        }

        .password-body {
            padding: 30px;
            background-color: #fff;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #4e4e4e;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
        }

        .form-control {
            border-left: none;
            padding-left: 5px;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #ced4da;
        }

        .btn-primary {
            background: linear-gradient(120deg, #4e73df 0%, #224abe 100%);
            border: none;
            padding: 10px 16px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: linear-gradient(120deg, #224abe 0%, #4e73df 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 3px;
            transition: all 0.3s ease;
        }

        .password-criteria {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 5px;
        }

        .password-criteria .valid {
            color: #198754;
        }

        .toggle-password {
            cursor: pointer;
            border-left: none;
        }

        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }

        .error-feedback {
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="password-container">
        <div class="password-card">
            <div class="password-header">
                <h4><i class="fas fa-key me-2"></i>Réinitialiser le mot de passe</h4>
                <p class="mb-0">Entrez votre nouveau mot de passe</p>
            </div>

            <div class="password-body">
                <form method="POST" action="{{ route('password.store') }}" class="needs-validation" novalidate>
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-medium">Adresse e-mail</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email', $request->email) }}"
                                placeholder="votre@email.com" required autofocus autocomplete="username">
                        </div>
                        @error('email')
                            <div class="error-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-medium">Nouveau mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                id="password" name="password" placeholder="Entrez votre nouveau mot de passe" required
                                autocomplete="new-password" pattern="^(?=.*[A-Za-z])(?=.*\d).{8,}$">
                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                aria-label="Afficher/masquer le mot de passe">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-criteria">
                            <div class="criteria-length" data-valid="false">• 8 caractères minimum</div>
                            <div class="criteria-letter" data-valid="false">• Au moins une lettre</div>
                            <div class="criteria-number" data-valid="false">• Au moins un chiffre</div>
                        </div>
                        <div class="password-strength bg-secondary mt-2"></div>
                        @error('password')
                            <div class="error-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-medium">Confirmer le mot de
                            passe</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password"
                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                id="password_confirmation" name="password_confirmation"
                                placeholder="Confirmez votre mot de passe" required autocomplete="new-password">
                            <button class="btn btn-outline-secondary toggle-password" type="button"
                                aria-label="Afficher/masquer le mot de passe">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="password-match" class="error-feedback"></div>
                        @error('password_confirmation')
                            <div class="error-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                        <i class="fas fa-sync-alt me-2"></i>Réinitialiser le mot de passe
                    </button>
                </form>

                <div class="back-to-login">
                    <p class="text-muted mb-0">Revenir à la
                        <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-medium">connexion</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.closest('.input-group').querySelector('input');
                    const icon = this.querySelector('i');

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.replace('fa-eye', 'fa-eye-slash');
                        this.setAttribute('aria-label', 'Masquer le mot de passe');
                    } else {
                        input.type = 'password';
                        icon.classList.replace('fa-eye-slash', 'fa-eye');
                        this.setAttribute('aria-label', 'Afficher le mot de passe');
                    }
                });
            });

            // Password strength indicator
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            const strengthBar = document.querySelector('.password-strength');
            const criteria = {
                length: document.querySelector('.criteria-length'),
                letter: document.querySelector('.criteria-letter'),
                number: document.querySelector('.criteria-number')
            };
            const passwordMatch = document.getElementById('password-match');

            passwordInput.addEventListener('input', function() {
                const password = this.value;

                // Check criteria
                const hasLength = password.length >= 8;
                const hasLetter = /[A-Za-z]/.test(password);
                const hasNumber = /\d/.test(password);

                // Update criteria indicators
                criteria.length.dataset.valid = hasLength;
                criteria.letter.dataset.valid = hasLetter;
                criteria.number.dataset.valid = hasNumber;

                // Update strength bar
                let strength = 0;
                if (hasLength) strength++;
                if (hasLetter) strength++;
                if (hasNumber) strength++;

                if (strength === 0) {
                    strengthBar.className = 'password-strength bg-secondary';
                    strengthBar.style.width = '0%';
                } else if (strength === 1) {
                    strengthBar.className = 'password-strength bg-danger';
                    strengthBar.style.width = '33%';
                } else if (strength === 2) {
                    strengthBar.className = 'password-strength bg-warning';
                    strengthBar.style.width = '66%';
                } else {
                    strengthBar.className = 'password-strength bg-success';
                    strengthBar.style.width = '100%';
                }

                // Check password match
                checkPasswordMatch();
            });

            confirmInput.addEventListener('input', checkPasswordMatch);

            function checkPasswordMatch() {
                if (confirmInput.value && passwordInput.value !== confirmInput.value) {
                    passwordMatch.textContent = 'Les mots de passe ne correspondent pas';
                } else {
                    passwordMatch.textContent = '';
                }
            }

            // Form validation
            const form = document.querySelector('.needs-validation');
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    </script>
</body>

</html>
