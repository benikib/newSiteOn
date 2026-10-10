@extends('layouts.main')
@section('title', 'Gestion des Réservations')
@section('content')

    <div class="container py-4">
        <!-- En-tête avec bouton d'ajout -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 text-dark">Gestion des Réservations</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReservationModal">
                <i class="fas fa-plus me-1"></i> Nouvelle Réservation
            </button>
        </div>

        <!-- Modal Ajout Réservation -->
        <div class="modal fade" id="addReservationModal" tabindex="-1" aria-labelledby="addReservationModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="addReservationModalLabel">Nouvelle Réservation</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('reservations.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="client_name" class="form-label">Nom du client <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="client_name" name="client_name"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="client_phone" class="form-label">Téléphone</label>
                                        <input type="tel" class="form-control" id="client_phone" name="client_phone">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="service_id" class="form-label">Service <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="service_id" name="service_id" required>
                                            <option value="">Sélectionnez un service</option>
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}">{{ $service->nom }} -
                                                    {{ $service->prix }} $</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date" class="form-label">Date et heure <span
                                                class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control" id="date" name="date"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes supplémentaires</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Ajout Paiement -->
        <div class="modal fade" id="paiementModal" tabindex="-1" aria-labelledby="paiementModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="paiementModalLabel">Enregistrer un Paiement</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="formPaiement" method="POST" action="{{ route('paiements.store') }}">
                        @csrf
                        <input type="hidden" name="reservation_id" id="reservation_id">
                        <input type="hidden" name="service_id" id="modal_service_id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Client</label>
                                <input type="text" class="form-control" name="client" id="modal_client_name"
                                    readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Service</label>
                                <input type="text" class="form-control" id="modal_service_name" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="montant" class="form-label">Montant <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="montant" name="montant" required>
                            </div>

                            <div class="mb-3">
                                <label for="date_paiement" class="form-label">Date du paiement <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_paiement" name="date_paiement"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-success">Enregistrer Paiement</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tableau des Réservations -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des Réservations</h5>
                <div class="input-group" style="width: 300px;">
                    <input type="text" class="form-control" placeholder="Rechercher..." id="searchInput">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="reservationsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Client</th>
                                <th>Téléphone</th>
                                <th>Service</th>
                                <th>Date/Heure</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reservations as $reservation)
                                <tr>
                                    <td>{{ $reservation->client_name }}</td>
                                    <td>{{ $reservation->client_phone ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $reservation->service->nom }}</span>
                                        <br>
                                        <small>{{ $reservation->service->prix }} $</small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($reservation->date)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $reservation->statut === 'confirmé' ? 'success' : ($reservation->statut === 'rejeté' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($reservation->statut) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        @if ($reservation->statut === 'en_attente')
                                            <button class="btn btn-sm btn-success me-1"
                                                onclick="changerStatut({{ $reservation->id }}, 'confirmé')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger"
                                                onclick="changerStatut({{ $reservation->id }}, 'rejeté')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @elseif($reservation->statut === 'confirmé')
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#paiementModal"
                                                data-reservation-id="{{ $reservation->id }}"
                                                data-client-name="{{ $reservation->client_name }}"
                                                data-service-id="{{ $reservation->service->id }}"
                                                data-service-name="{{ $reservation->service->nom }}"
                                                data-service-prix="{{ $reservation->service->prix }}">
                                                <i class="fas fa-money-bill-wave"></i> Payer
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Aucune réservation trouvée</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($reservations->hasPages())
                    <div class="card-footer">
                        {{ $reservations->links() }}
                    </div>
                @endif
            </div>
        </div>

        <section class="mt-5" aria-labelledby="product-reservations-title">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                <div>
                    <h2 class="h5 mb-1" id="product-reservations-title">Demandes de réservation d’articles</h2>
                    <p class="text-muted small mb-0">Les demandes en attente sont déduites de la disponibilité publique.</p>
                </div>
            </div>
            <div class="table-responsive bg-white border rounded">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Reçue le</th><th>Client</th><th>Téléphone</th><th>Message</th><th>Articles demandés</th><th>Statut</th><th class="text-end">Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($productReservations as $productReservation)
                            <tr>
                                <td>{{ $productReservation->created_at->format('d/m/Y H:i') }}</td>
                                <td class="fw-semibold">{{ $productReservation->client_name }}</td>
                                <td><a href="tel:{{ $productReservation->client_phone }}">{{ $productReservation->client_phone }}</a></td>
                                <td>{{ $productReservation->message ?: '—' }}</td>
                                <td>
                                    @foreach($productReservation->items as $item)
                                        <div>{{ $item->product->name ?? 'Article indisponible' }} <span class="text-muted">({{ $item->product->code ?? '—' }}) × {{ $item->quantity }}</span></div>
                                    @endforeach
                                </td>
                                <td>
                                    <span class="badge {{ $productReservation->statut === 'confirmé' ? 'bg-success' : ($productReservation->statut === 'rejeté' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                        {{ $productReservation->statut === 'confirmé' ? 'Confirmée' : ($productReservation->statut === 'rejeté' ? 'Refusée' : 'En attente') }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if($productReservation->statut === 'en_attente')
                                        <form action="{{ route('etablissements.reservation.articles.statut', $productReservation->id) }}" method="POST" class="d-inline-flex gap-1">
                                            @csrf
                                            <button class="btn btn-sm btn-success" type="submit" name="statut" value="confirmé" aria-label="Confirmer la demande">
                                                <i class="fas fa-check me-1" aria-hidden="true"></i> Confirmer
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" type="submit" name="statut" value="rejeté" aria-label="Refuser la demande">
                                                <i class="fas fa-times me-1" aria-hidden="true"></i> Refuser
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">Demande traitée</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Aucune demande de réservation d’article.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($productReservations->hasPages())
                <div class="mt-3">{{ $productReservations->links() }}</div>
            @endif
        </section>

        <section class="mt-5" aria-labelledby="delivery-orders-title">
            <div class="mb-3">
                <h2 class="h5 mb-1" id="delivery-orders-title">Commandes à livrer</h2>
                <p class="text-muted small mb-0">Confirmez les paiements, préparez les commandes et suivez leur livraison.</p>
            </div>
            <div class="table-responsive bg-white border rounded">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr><th>Commande</th><th>Client et adresse</th><th>Articles</th><th>Paiement</th><th>Statut</th><th class="text-end">Action suivante</th></tr>
                    </thead>
                    <tbody>
                        @forelse($deliveryOrders as $deliveryOrder)
                            @php
                                $deliveryStatusLabels = [
                                    'en_attente' => 'En attente',
                                    'payee_partiellement' => 'Payée partiellement',
                                    'payee' => 'Payée',
                                    'en_livraison' => 'En livraison',
                                    'livree' => 'Livrée',
                                ];
                            @endphp
                            <tr>
                                <td>
                                    <strong>#{{ $deliveryOrder->id }}</strong><br>
                                    <small class="text-muted">{{ $deliveryOrder->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <strong>{{ $deliveryOrder->client_name }}</strong><br>
                                    <a href="tel:{{ $deliveryOrder->client_phone }}">{{ $deliveryOrder->client_phone }}</a><br>
                                    <small class="text-muted">{{ $deliveryOrder->delivery_address }}</small>
                                </td>
                                <td>
                                    @foreach($deliveryOrder->items as $item)
                                        <div>{{ $item->product_name }} <span class="text-muted">× {{ $item->quantity }}</span></div>
                                    @endforeach
                                </td>
                                <td>
                                    <div>Total : {{ number_format($deliveryOrder->total_amount, 2, ',', ' ') }} CDF</div>
                                    @if($deliveryOrder->payment_plan === 'two_installments')
                                        <small class="text-muted">Acompte prévu : {{ number_format($deliveryOrder->deposit_amount, 2, ',', ' ') }} CDF</small><br>
                                    @else
                                        <small class="text-muted">Paiement en une fois</small><br>
                                    @endif
                                    <small class="text-muted">Reçu : {{ number_format($deliveryOrder->amount_paid, 2, ',', ' ') }} CDF</small>
                                </td>
                                <td>
                                    <span class="badge {{ $deliveryOrder->statut === 'livree' ? 'bg-success' : ($deliveryOrder->statut === 'en_attente' ? 'bg-warning text-dark' : 'bg-info text-dark') }}">
                                        {{ $deliveryStatusLabels[$deliveryOrder->statut] ?? $deliveryOrder->statut }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @php
                                        $nextAction = match($deliveryOrder->statut) {
                                            'en_attente' => 'confirm_payment',
                                            'payee_partiellement' => 'confirm_balance',
                                            'payee' => 'start_delivery',
                                            'en_livraison' => 'mark_delivered',
                                            default => null,
                                        };
                                        $nextActionLabel = match($nextAction) {
                                            'confirm_payment' => $deliveryOrder->payment_plan === 'two_installments' ? 'Confirmer l’acompte' : 'Confirmer le paiement',
                                            'confirm_balance' => 'Confirmer le solde',
                                            'start_delivery' => 'Mettre en livraison',
                                            'mark_delivered' => 'Marquer livrée',
                                            default => null,
                                        };
                                    @endphp
                                    @if($nextAction)
                                        <form action="{{ route('etablissements.livraisons.action', $deliveryOrder->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="{{ $nextAction }}">
                                            <button class="btn btn-sm btn-outline-primary" type="submit">{{ $nextActionLabel }}</button>
                                        </form>
                                    @else
                                        <span class="text-muted small">Terminée</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Aucune commande à livrer.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($deliveryOrders->hasPages())
                <div class="mt-3">{{ $deliveryOrders->links() }}</div>
            @endif
        </section>
    </div>

    <script>
        // Gestion du modal de paiement
        document.getElementById('paiementModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const modal = this;

            // Récupération des données
            modal.querySelector('#reservation_id').value = button.getAttribute('data-reservation-id');
            modal.querySelector('#modal_client_name').value = button.getAttribute('data-client-name');
            modal.querySelector('#modal_service_id').value = button.getAttribute('data-service-id');
            modal.querySelector('#modal_service_name').value = button.getAttribute('data-service-name');
            modal.querySelector('#montant').value = button.getAttribute('data-service-prix');
        });

        // Fonction pour changer le statut
        function changerStatut(id, statut) {
            if (!confirm(`Voulez-vous vraiment marquer cette réservation comme "${statut}" ?`)) return;

            fetch(`/reservations/${id}/statut`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        statut
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.BisikaAlerts.success(data.message || 'Le statut de la réservation a été mis à jour.');
                        location.reload();
                    } else {
                        window.BisikaAlerts.error('Le statut de la réservation n’a pas pu être mis à jour.');
                    }
                })
                .catch(error => {
                    console.error(error);
                    window.BisikaAlerts.error('La réservation n’a pas pu être mise à jour. Vérifiez votre connexion.');
                });
        }

        // Recherche dans le tableau
        document.getElementById('searchInput').addEventListener('input', function() {
            const value = this.value.toLowerCase();
            document.querySelectorAll('#reservationsTable tbody tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(value) ? '' : 'none';
            });
        });
    </script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector('#addReservationModal form');

    form.addEventListener("submit", async (e) => {
        e.preventDefault(); // 👉 empêche le navigateur d'aller sur l'URL

        const url = form.action;   // garde le route('reservations.store')
        const formData = new FormData(form);

        try {
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            if (response.ok) {
                const data = await response.json();
                window.BisikaAlerts.success(data.message || 'Réservation enregistrée avec succès.');

                // Fermer la modale Bootstrap
                const modal = bootstrap.Modal.getInstance(document.getElementById('addReservationModal'));
                modal.hide();

                // Réinitialiser le formulaire
                form.reset();
            } else if (response.status === 422) {
                const errors = await response.json();
                console.warn(errors.errors);
                window.BisikaAlerts.error('Certaines informations sont invalides. Vérifiez le formulaire.');
            } else {
                window.BisikaAlerts.error('La réservation n’a pas pu être enregistrée. Réessayez.');
            }
        } catch (error) {
            console.error(error);
            window.BisikaAlerts.error('Impossible de contacter le serveur. Vérifiez votre connexion.');
        }
    });
});
</script>

@endsection
