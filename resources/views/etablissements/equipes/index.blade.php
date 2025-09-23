@extends('layouts.main')
@section('title', 'Équipes - Gestion des Établissements')
@section('content')

    <div class="container py-4">
        <!-- En-tête avec bouton d'ajout -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestion des Équipes</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEquipeModal">
                <i class="fas fa-plus me-1"></i> Nouvelle équipe
            </button>
        </div>

        <!-- Modal d'ajout d'équipe -->
        <div class="modal fade" id="addEquipeModal" tabindex="-1" aria-labelledby="addEquipeModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="addEquipeModalLabel">Créer une équipe</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('equipes.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nom" class="form-label">Taches de l'équipe *</label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom"
                                    name="nom" value="{{ old('nom') }}" required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>



                                <div class="mb-3">
                                    <label class="form-label">Reservation</label>

                                    <select class="form-select" name="evenement">
                                           @foreach($reservations as $reservation)
                                        <option value="{{ $reservation->service->nom }} - {{ $reservation->date}}">
                                            {{ $reservation->service->nom }} - {{ $reservation->date}}
                                        </option>
                                            @endforeach
                                    </select>
                                </div>


                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Membres</label>
                                <select class="form-select select2" name="personnels[]" multiple>
                                    @foreach ($personnels as $personnel)
                                        <option value="{{ $personnel->id }}">
                                            {{ $personnel->nom }} ({{ $personnel->poste ?? 'Sans poste' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="hidden" name="etablissement_id" value="{{ auth()->user()->etablissement_id }}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tableau des équipes -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Taches</th>
                                <th>Evenements</th>
                                <th>Description</th>
                                <th>Membres</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($equipes as $equipe)
                                <tr>
                                    <td>{{ $equipe->nom }}</td>
                                    <td>{{ $equipe->evenement ?? 'Aucun evenement' }}</td>
                                    <td>{{ $equipe->description ?? 'Aucune description' }}</td>
                                    <td>
                                        @forelse($equipe->personnels as $personnel)
                                            <span class="badge bg-primary">{{ $personnel->nom }}</span>
                                        @empty
                                            <span class="text-muted">Aucun membre</span>
                                        @endforelse
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex">
                                            <button class="btn btn-sm btn-warning me-1" data-bs-toggle="modal"
                                                data-bs-target="#editEquipeModal{{ $equipe->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('equipes.destroy', $equipe->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Supprimer cette équipe ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal d'édition pour chaque équipe -->
                                <div class="modal fade" id="editEquipeModal{{ $equipe->id }}" tabindex="-1"
                                    aria-labelledby="editEquipeModalLabel{{ $equipe->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning text-white">
                                                <h5 class="modal-title" id="editEquipeModalLabel{{ $equipe->id }}">
                                                    Modifier  la tâches
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('equipes.update', $equipe->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="nom" class="form-label">Tâches de l'equipe</label>
                                                        <input type="text" class="form-control" id="nom"
                                                            name="nom" value="{{ $equipe->nom }}" required>
                                                    </div>


                                                     <div class="mb-3">
                                                        <label class="form-label">Reservation</label>
                                                                <select class="form-select" name="evenement">
                                           @foreach($reservations as $reservation)
                                        <option value="{{ $reservation->service->nom }} - {{ $reservation->date}}">
                                            {{ $reservation->service->nom }} - {{ $reservation->date}}
                                        </option>
                                            @endforeach
                                    </select>
                                                    </div>


                                                    <div class="mb-3">
                                                        <label for="description" class="form-label">Description</label>
                                                        <textarea class="form-control" id="description" name="description">{{ $equipe->description }}</textarea>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Membres</label>
                                                        <select class="form-select select2" name="personnels[]" multiple>
                                                            @foreach ($personnels as $personnel)
                                                                <option value="{{ $personnel->id }}"
                                                                    {{ $equipe->personnels->contains($personnel->id) ? 'selected' : '' }}>
                                                                    {{ $personnel->nom }}
                                                                    ({{ $personnel->poste ?? 'Sans poste' }})
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-warning">Mettre à jour</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">Aucune équipe enregistrée</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($equipes->hasPages())
                    <div class="card-footer d-flex justify-content-center">
                        {{ $equipes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Script pour Select2 -->
    @section('scripts')
        <script>
            $(document).ready(function() {
                $('.select2').select2({
                    placeholder: "Sélectionnez des membres",
                    width: '100%'
                });
            });
        </script>
    @endsection

@endsection
