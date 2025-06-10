@foreach ($etablissements as $etablissement)
    <div class="modal fade" id="editModal{{ $etablissement->id }}" tabindex="-1"
        aria-labelledby="editModalLabel{{ $etablissement->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel{{ $etablissement->id }}">Modifier l'établissement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form action="{{ route('etablissements.update', $etablissement->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <!-- Onglets de navigation -->
                        <ul class="nav nav-tabs mb-4" id="editTabs{{ $etablissement->id }}" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="edit-etablissement-tab-{{ $etablissement->id }}"
                                    data-bs-toggle="tab" data-bs-target="#edit-etablissement-{{ $etablissement->id }}"
                                    type="button" role="tab" aria-controls="edit-etablissement"
                                    aria-selected="true">Établissement</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="edit-localisation-tab-{{ $etablissement->id }}"
                                    data-bs-toggle="tab" data-bs-target="#edit-localisation-{{ $etablissement->id }}"
                                    type="button" role="tab" aria-controls="edit-localisation"
                                    aria-selected="false">Localisation</button>
                            </li>
                        </ul>

                        <!-- Contenu des onglets -->
                        <div class="tab-content" id="editTabsContent{{ $etablissement->id }}">
                            <!-- Onglet Établissement -->
                            <div class="tab-pane fade show active" id="edit-etablissement-{{ $etablissement->id }}"
                                role="tabpanel" aria-labelledby="edit-etablissement-tab-{{ $etablissement->id }}">
                                <div class="mb-3">
                                    <label for="edit_nom{{ $etablissement->id }}" class="form-label">Nom de
                                        l'Établissement*</label>
                                    <input type="text" name="nom" id="edit_nom{{ $etablissement->id }}"
                                        class="form-control" value="{{ $etablissement->nom }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_telephone{{ $etablissement->id }}"
                                        class="form-label">Téléphone*</label>
                                    <input type="text" name="telephone" id="edit_telephone{{ $etablissement->id }}"
                                        class="form-control" value="{{ $etablissement->telephone }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_description{{ $etablissement->id }}"
                                        class="form-label">Description</label>
                                    <textarea name="description" id="edit_description{{ $etablissement->id }}" class="form-control" rows="3">{{ $etablissement->description }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_type{{ $etablissement->id }}" class="form-label">Type
                                        d'Établissement*</label>
                                    <select name="type_etablissement_id" id="edit_type{{ $etablissement->id }}"
                                        class="form-select" required>
                                        <option value="" disabled>Sélectionnez un type</option>
                                        @foreach ($typeEtablissements as $type)
                                            <option value="{{ $type->id }}"
                                                {{ $etablissement->type_etablissement_id == $type->id ? 'selected' : '' }}>
                                                {{ $type->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="button" class="btn btn-primary"
                                        onclick="nextEditTab('edit-localisation-tab-{{ $etablissement->id }}', 'edit-etablissement-{{ $etablissement->id }}')">Suivant</button>
                                </div>
                            </div>

                            <!-- Onglet Localisation -->
                            <div class="tab-pane fade" id="edit-localisation-{{ $etablissement->id }}" role="tabpanel"
                                aria-labelledby="edit-localisation-tab-{{ $etablissement->id }}">
                                <div class="mb-3">
                                    <label for="edit_quartier{{ $etablissement->id }}"
                                        class="form-label">Quartier*</label>
                                    <input type="text" name="quartier" id="edit_quartier{{ $etablissement->id }}"
                                        class="form-control" value="{{ $etablissement->quartier }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_commune{{ $etablissement->id }}"
                                        class="form-label">Commune*</label>
                                    <input type="text" name="commune" id="edit_commune{{ $etablissement->id }}"
                                        class="form-control" value="{{ $etablissement->commune }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_avenue{{ $etablissement->id }}"
                                        class="form-label">Avenue*</label>
                                    <input type="text" name="avenue" id="edit_avenue{{ $etablissement->id }}"
                                        class="form-control" value="{{ $etablissement->avenue }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_ville{{ $etablissement->id }}" class="form-label">Ville*</label>
                                    <input type="text" name="ville" id="edit_ville{{ $etablissement->id }}"
                                        class="form-control" value="{{ $etablissement->ville }}" required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_numero{{ $etablissement->id }}"
                                        class="form-label">Numéro*</label>
                                    <input type="text" name="numero" id="edit_numero{{ $etablissement->id }}"
                                        class="form-control" value="{{ $etablissement->numero }}" required>
                                </div>

                                <div id="editStatusMessage{{ $etablissement->id }}" class="alert alert-danger d-none"
                                    role="alert">
                                    <p class="mb-0"></p>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-secondary"
                                        onclick="prevEditTab('edit-etablissement-tab-{{ $etablissement->id }}', 'edit-localisation-{{ $etablissement->id }}')">Précédent</button>
                                    <button type="submit" class="btn btn-success">Mettre à jour</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Fonctions spécifiques à l'édition
        function nextEditTab(nextTabId, currentPaneId) {
            // Valider le formulaire avant de passer à l'onglet suivant
            const currentTab = document.getElementById(currentPaneId);
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
                const tab = new bootstrap.Tab(nextTab);
                tab.show();
            }
        }

        function prevEditTab(prevTabId, currentPaneId) {
            const prevTab = document.getElementById(prevTabId);
            const tab = new bootstrap.Tab(prevTab);
            tab.show();
        }
    </script>
@endforeach
