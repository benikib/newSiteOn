<div class="modal fade" id="editEtablissementModal" tabindex="-1" aria-labelledby="editEtablissementModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="editEtablissementModalLabel">Modifier l'Établissement</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <form action="{{ route('etablissements.update', $etablissement->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-body">

          <!-- Section Photo principale -->
          <div class="info-section mb-4">
            <h6 class="section-title text-primary mb-3">
              <i class="fas fa-camera me-2"></i>Photo principale
            </h6>
            <div class="main-photo-container text-center">
              @if(false)
                <img src="{{ asset('storage/'.$etablissement->medias[0]->path) }}"
                     class="img-fluid rounded main-gallery-image mb-3"
                     id="currentMainImage"
                     style="max-height: 200px; width: auto;">
              @else
                <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
                     class="img-fluid rounded main-gallery-image mb-3"
                     id="currentMainImage"
                     style="max-height: 200px; width: 100%; object-fit: cover;">
              @endif
              <div class="form-group">
                <label for="newImage" class="form-label">Changer la photo</label>
                <input type="file" class="form-control" id="newImage" name="image" accept="image/*">
                <small class="text-muted">Format recommandé : JPG/PNG, max 2MB</small>
              </div>
            </div>
          </div>

          <!-- Section Établissement -->
          <div class="info-section mb-4">
            <h6 class="section-title text-primary mb-3">
              <i class="fas fa-building me-2"></i>Informations de l'établissement
            </h6>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group mb-3">
                  <label for="editNom" class="form-label">Nom*</label>
                  <input type="text" class="form-control" id="editNom" name="nom" value="{{ $etablissement->nom }}" required>
                </div>
                <div class="form-group mb-3">
                  <label for="editType" class="form-label">Type*</label>
                  <select class="form-select" id="editType" name="type_etablissement_id" required>
                    @foreach($typeEtablissements as $type)
                      <option value="{{ $type->id }}" {{ $etablissement->type_etablissement_id == $type->id ? 'selected' : '' }}>
                        {{ $type->nom }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-3">
                  <label for="editTelephone" class="form-label">Téléphone</label>
                  <input type="text" class="form-control" id="editTelephone" name="telephone" value="{{ $etablissement->telephone }}">
                </div>
                <div class="form-group">
                  <label for="editDescription" class="form-label">Description</label>
                  <textarea class="form-control" id="editDescription" name="description" rows="3">{{ $etablissement->description }}</textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Section Services -->
          <div class="info-section mb-4">
            <h6 class="section-title text-primary mb-3">
              <i class="fas fa-concierge-bell me-2"></i>Services
            </h6>
            <div class="form-group">
              {{-- <div class="d-flex flex-wrap">
                @foreach($allServices as $service)
                  <div class="form-check me-3 mb-2">
                    <input class="form-check-input" type="checkbox" 
                           id="service{{ $service->id }}" 
                           name="services[]" 
                           value="{{ $service->id }}"
                           {{ $etablissement->services->contains($service->id) ? 'checked' : '' }}>
                    <label class="form-check-label" for="service{{ $service->id }}">
                      {{ $service->nom }}
                    </label>
                  </div>
                @endforeach
              </div> --}}
            </div>
          </div>

          <!-- Section Adresse -->
          <div class="info-section mb-4">
            <h6 class="section-title text-primary mb-3">
              <i class="fas fa-map-marker-alt me-2"></i>Adresse
            </h6>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group mb-3">
                  <label for="editVille" class="form-label">Ville*</label>
                  <input type="text" class="form-control" id="editVille" name="ville" value="{{ $etablissement->ville }}" required>
                </div>
                <div class="form-group mb-3">
                  <label for="editCommune" class="form-label">Commune*</label>
                  <input type="text" class="form-control" id="editCommune" name="commune" value="{{ $etablissement->commune }}" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group mb-3">
                  <label for="editQuartier" class="form-label">Quartier*</label>
                  <input type="text" class="form-control" id="editQuartier" name="quartier" value="{{ $etablissement->quartier }}" required>
                </div>
                <div class="form-group">
                  <label for="editAvenue" class="form-label">Avenue*</label>
                  <div class="input-group">
                    <input type="text" class="form-control" id="editAvenue" name="avenue" value="{{ $etablissement->avenue }}" required>
                    <span class="input-group-text">N°</span>
                    <input type="text" class="form-control" style="max-width: 80px;" name="numero" value="{{ $etablissement->numero }}" required>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Script pour prévisualiser la nouvelle image -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const newImageInput = document.getElementById('newImage');
  const currentMainImage = document.getElementById('currentMainImage');
  
  if (newImageInput && currentMainImage) {
    newImageInput.addEventListener('change', function(e) {
      if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(event) {
          currentMainImage.src = event.target.result;
          currentMainImage.style.objectFit = 'cover';
        }
        
        reader.readAsDataURL(e.target.files[0]);
      }
    });
  }
});
</script>