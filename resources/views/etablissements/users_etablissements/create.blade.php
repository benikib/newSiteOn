<div class="modal fade" id="repportingModal" tabindex="-1" aria-labelledby="repportingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-gradient-success">
                <h5 class="modal-title" id="repportingModalLabel">Ajouter un users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <form action="{{ route('users_ets.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="telphone" class="form-label">Téléphone</label>
                        <input type="text" class="form-control" id="telphone" name="telephone" required>
                    </div>
                    <input type="hidden" name="role" value="etablissement">
                    <input type="hidden" name="etablissement_id" value="{{ $userEtablissements->first()->etablissement_id }}">
                    <div class="mb-3 position-relative">
    <label for="password" class="form-label">Mot de passe (laisser vide pour ne pas modifier)</label>
    <div class="input-group">
        <input type="password" class="form-control" id="password" name="password">
        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
            <i class="bi bi-eye"></i>
        </button>
    </div>
</div>

<!-- Bootstrap Icons si pas déjà inclus -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<script>
document.getElementById('togglePassword').addEventListener('click', function () {
    const passwordInput = document.getElementById('password');
    const icon = this.querySelector('i');

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.remove("bi-eye");
        icon.classList.add("bi-eye-slash");
    } else {
        passwordInput.type = "password";
        icon.classList.remove("bi-eye-slash");
        icon.classList.add("bi-eye");
    }
});
</script>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </div>
            </form>
        </div>
    </div>
</div>
</div>
