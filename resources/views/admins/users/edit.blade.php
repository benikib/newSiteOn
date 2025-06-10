@foreach ($users as $user)
    <div class="modal fade" id="editModal{{ $user->id }}" tabindex="-1"
        aria-labelledby="editModalLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel{{ $user->id }}">Modifier l'utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ $user->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ $user->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="telphone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control" id="telphone" name="telephone"
                                value="{{ $user->telephone }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="" disabled>Choisir un rôle</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="etablissement" {{ $user->role == 'etablissement' ? 'selected' : '' }}>
                                    Etablissement</option>
                                <option value="client" {{ $user->role == 'client' ? 'selected' : '' }}>Client</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe (laisser vide pour ne pas
                                modifier)</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>


                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
