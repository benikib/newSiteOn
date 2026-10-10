<div class="modal fade" id="repportingModal" tabindex="-1" aria-labelledby="repportingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-gradient-success">
        <h5 class="modal-title" id="repportingModalLabel">Ajouter une session</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <form action="{{ route('services.store',$etablissement->id) }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label for="name" class="form-label">Nom du Service</label>
            <input type="text" name="nom" id="name" class="form-control" placeholder="Ex: Service de santé, Transport, etc." required>  
            </div>
            <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="3" placeholder="Description du service"></textarea>
          </div>
<input type="hidden" name="etablissement_id" value="{{ $etablissement->id }}">
          <div class=""></div>
            <div class="mb-3">
            <label for="prix" class="form-label">Prix</label>
            <input type="number" name="prix" id="prix" class="form-control" placeholder="Ex: 1000" required>
            </div>
            <div class="mb-3">
              <label for="service_icone" class="form-label">Icône</label>
              <select name="icone" id="service_icone" class="form-select">
                <option value="calendar">Réservation</option>
                <option value="truck">Livraison</option>
                <option value="phone">Contact</option>
                <option value="star">Autre service</option>
              </select>
            </div>
            <input type="hidden" name="disponibilite" value="0">
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" name="disponibilite" id="service_disponibilite" value="1" checked>
              <label class="form-check-label" for="service_disponibilite">Service actif sur le site public</label>
            </div>
            <div id="statusMessage" class="alert alert-danger d-none" role="alert">
                <p class="mb-0"></p>
            </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Ajouter</button>
        </div>
      </form>
    </div>
    </div>
  </div>

