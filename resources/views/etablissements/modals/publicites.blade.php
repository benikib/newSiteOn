<div class="modal fade" id="publiciteModal{{ $etablissement->id }}" tabindex="-1" role="dialog"
    aria-labelledby="publiciteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-success">
                <h5 class="modal-title text-white" id="publiciteModalLabel">Publicités pour {{ $etablissement->nom }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6>Campagnes publicitaires</h6>
                    <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                        data-bs-target="#addPubliciteModal{{ $etablissement->id }}">
                        <i class="fas fa-plus me-1"></i> Nouvelle campagne
                    </button>
                </div>

                @if ($etablissement->publicites && $etablissement->publicites->count() > 0)
                    <div class="row">
                        @foreach ($etablissement->publicites as $publicite)
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">

                                    <div class="card-body">
                                        <h5 class="card-title">{{ $publicite->titre }}</h5>
                                        <p class="card-text">{{ Str::limit($publicite->description, 100) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span
                                                class="badge bg-gradient-info">{{ $publicite->statut ?? 'En attente' }}</span>
                                            <small class="text-muted">
                                                Du {{ $publicite->date }} [
                                                {{-- {{ $publicite->date->addDays(3)->format('d/m/Y') }}] --}}
                                                {{-- Assuming date is a Carbon instance --}}
                                                {{-- If not, you might need to format it accordingly --}}
                                                {{-- You can also use $publicite->date->diffForHumans() for relative time --}}
                                                pendant
                                                {{ $publicite->dure }}

                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="btn-group w-100">

                                            {{-- <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#editPubliciteModal{{ $publicite->id }}">
                                                <i class="fas fa-edit"></i> Voir
                                            </button> --}}
                                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal"
                                                data-bs-target="#viewPubliciteModal{{ $publicite->id }}">
                                                <i class="fas fa-eye"></i> Modifier
                                            </button>
                                            <form action="{{ route('publicite.destroy', $publicite->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publicité?')">
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        Aucune campagne publicitaire pour cet établissement.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

@include('etablissements.modals.add-publicite', ['etablissement' => $etablissement])

@foreach ($etablissement->publicites as $publicite)
    @include('etablissements.modals.view-publicite', ['publicite' => $publicite])
    @include('etablissements.modals.edit-publicite', ['publicite' => $publicite])
@endforeach
