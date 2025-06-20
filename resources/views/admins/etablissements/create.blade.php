<div class="modal fade" id="repportingModal" tabindex="-1" aria-labelledby="repportingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="repportingModalLabel">Ajouter un établissement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form action="{{ route('etablissements.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Onglets de navigation -->
                    <ul class="nav nav-tabs mb-4" id="formTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="etablissement-tab" data-bs-toggle="tab"
                                data-bs-target="#etablissement" type="button" role="tab"
                                aria-controls="etablissement" aria-selected="true">Établissement</button>
                        </li>
                        @if (auth()->user()->role === 'admin')
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin"
                                    type="button" role="tab" aria-controls="admin" aria-selected="false"
                                    disabled>Administrateur</button>
                            </li>
                        @endif
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="localisation-tab" data-bs-toggle="tab"
                                data-bs-target="#localisation" type="button" role="tab"
                                aria-controls="localisation" aria-selected="false"
                                {{ auth()->user()->role === 'admin' ? 'disabled' : '' }}>Localisation</button>
                        </li>
                    </ul>

                    <!-- Contenu des onglets -->
                    <div class="tab-content" id="formTabsContent">
                        <!-- Onglet Établissement -->
                        <div class="tab-pane fade show active" id="etablissement" role="tabpanel"
                            aria-labelledby="etablissement-tab">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Nom de l'Établissement*</label>
                                <input type="text" name="nom" id="nom" class="form-control"
                                    placeholder="Ex: Université, Lycée, etc." required
                                    oninput="@if (auth()->user()->role === 'admin') generateUserCredentials(this.value) @endif">
                            </div>

                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone*</label>
                                <input type="text" name="telephone" id="telephone" class="form-control"
                                    placeholder="Ex: 0123456789" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control" rows="3"
                                    placeholder="Description de l'établissement"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="type_etablissement_id" class="form-label">Type d'Établissement*</label>
                                <select name="type_etablissement_id" id="type_etablissement_id" class="form-select"
                                    required>
                                    <option value="" disabled selected>Sélectionnez un type</option>
                                    @foreach ($typeEtablissements as $type)
                                        <option value="{{ $type->id }}">{{ $type->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-primary"
                                    onclick="nextTab('{{ auth()->user()->role === 'admin' ? 'admin-tab' : 'localisation-tab' }}')">Suivant</button>
                            </div>
                        </div>

                        @if (auth()->user()->role === 'admin')
                            <!-- Onglet Administrateur -->
                            <div class="tab-pane fade" id="admin" role="tabpanel" aria-labelledby="admin-tab">
                                <div class="mb-3">
                                    <label for="user_name" class="form-label">Nom de l'administrateur*</label>
                                    <input type="text" name="user_name" id="user_name" class="form-control"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="user_email" class="form-label">Email*</label>
                                    <input type="email" name="user_email" id="user_email" class="form-control"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label for="user_password" class="form-label">Mot de passe*</label>
                                    <input type="password" name="user_password" id="user_password"
                                        class="form-control" required>
                                    <small class="text-muted">Le mot de passe sera généré automatiquement mais peut
                                        être modifié</small>
                                </div>

                                <input type="hidden" name="user_role" value="etablissement">

                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-secondary"
                                        onclick="prevTab('etablissement-tab')">Précédent</button>
                                    <button type="button" class="btn btn-primary"
                                        onclick="nextTab('localisation-tab')">Suivant</button>
                                </div>
                            </div>
                        @endif

                        <!-- Onglet Localisation -->
                        <div class="tab-pane fade" id="localisation" role="tabpanel"
                            aria-labelledby="localisation-tab">
                            <div class="mb-3">
                                <label for="quartier" class="form-label">Quartier*</label>
                                <input type="text" name="quartier" id="quartier" class="form-control"
                                    placeholder="Ex: Quartier Latin, Montmartre, etc." required>
                            </div>

                            <div class="mb-3">
                                <label for="commune" class="form-label">Commune*</label>
                                <input type="text" name="commune" id="commune" class="form-control"
                                    placeholder="Ex: Paris, Lyon, etc." required>
                            </div>

                            <div class="mb-3">
                                <label for="avenue" class="form-label">Avenue*</label>
                                <input type="text" name="avenue" id="avenue" class="form-control"
                                    placeholder="Ex: Avenue des Champs-Élysées" required>
                            </div>

                            <div class="mb-3">
                                <label for="ville" class="form-label">Ville*</label>
                                <input type="text" name="ville" id="ville" class="form-control"
                                    placeholder="Ex: Paris, Lyon, etc." required>
                            </div>

                            <div class="mb-3">
                                <label for="numero" class="form-label">Numéro*</label>
                                <input type="text" name="numero" id="numero" class="form-control"
                                    placeholder="Ex: 123, 456, etc." required>
                            </div>

                            <div id="statusMessage" class="alert alert-danger d-none" role="alert">
                                <p class="mb-0"></p>
                            </div>
                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary"
                                    onclick="prevTab('{{ auth()->user()->role === 'admin' ? 'admin-tab' : 'etablissement-tab' }}')">Précédent</button>
                                <button type="submit" class="btn btn-success">Enregistrer</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Fonction pour générer les identifiants utilisateur
    function generateUserCredentials(etablissementName) {
        if (!etablissementName) return;

        const cleanedName = etablissementName
            .toLowerCase()
            .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
            .replace(/[^a-z0-9]/g, "_")
            .replace(/_+/g, "_")
            .replace(/^_+|_+$/g, "");

        document.getElementById('user_name').value = etablissementName;
        document.getElementById('user_email').value = cleanedName + '@gmail.com';
        document.getElementById('user_password').value = cleanedName + '@243';
    }

    // Navigation entre les onglets
    function nextTab(nextTabId) {
        // Valider le formulaire avant de passer à l'onglet suivant
        const currentTab = document.querySelector('.tab-pane.show.active');
        const inputs = currentTab.querySelectorAll('[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.value) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (isValid) {
            const nextTab = document.getElementById(nextTabId);
            if (nextTab) {
                nextTab.removeAttribute('disabled');
                const tab = new bootstrap.Tab(nextTab);
                tab.show();
            }
        }
    }

    function prevTab(prevTabId) {
        const prevTab = document.getElementById(prevTabId);
        const tab = new bootstrap.Tab(prevTab);
        tab.show();
    }
</script>

<style>
    .nav-tabs .nav-link.disabled {
        color: #6c757d;
        pointer-events: none;
    }

    .is-invalid {
        border-color: #dc3545 !important;
    }

    .is-invalid+.invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875em;
    }
</style>
