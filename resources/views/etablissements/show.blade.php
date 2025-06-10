<div class="modal fade" id="etablissementInfoModal" tabindex="-1" aria-labelledby="etablissementInfoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="etablissementInfoModalLabel">Fiche Établissement</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Fermer"></button>
            </div>
            <div class="modal-body">

                <!-- Section Photo principale -->
                <div class="info-section mb-4">
                    <div class="main-photo-container text-center">
                        @if (false)
                            <img src="{{ asset('storage/' . $etablissement->medias[0]->path) }}"
                                class="img-fluid rounded main-gallery-image"
                                alt="Photo principale de {{ $etablissement->nom }}"
                                style="max-height: 300px; width: auto;">
                        @else
                            <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
                                class="img-fluid rounded main-gallery-image" alt="Image par défaut"
                                style="max-height: 300px; width: 100%; object-fit: cover;">
                            <p class="text-muted mt-2"><small>Aucune photo disponible - Image illustrative</small></p>
                        @endif
                    </div>
                </div>

                <!-- Section Établissement -->
                <div class="info-section mb-4">
                    <h6 class="section-title text-primary mb-3">
                        <i class="fas fa-building me-2"></i>Établissement
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nom :</strong> {{ $etablissement->nom }}</p>
                            <p><strong>Type :</strong> {{ $etablissement->typeEtablissement->nom }}</p>
                            <p><strong>Dimensions :</strong> {{ $etablissement->dimensions ?? 'Non spécifié' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Téléphone :</strong> {{ $etablissement->telephone ?? 'Non renseigné' }}</p>
                            <p><strong>Description :</strong> {{ $etablissement->description ?? 'Aucune description' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section Services -->
                <div class="info-section mb-4">
                    <h6 class="section-title text-primary mb-3">
                        <i class="fas fa-concierge-bell me-2"></i>Services
                    </h6>
                    <div class="service-list">
                        @forelse($etablissement->services as $service)
                            <span class="badge bg-light text-dark me-2 mb-2 p-2">
                                <i class="fas fa-check-circle text-success me-1"></i>
                                {{ $service->nom }}
                            </span>
                        @empty
                            <p class="text-muted">Aucun service enregistré</p>
                        @endforelse
                    </div>
                </div>

                {{-- <!-- Section Galerie améliorée -->
        <div class="info-section mb-4">
          <h6 class="section-title text-primary mb-3">
            <i class="fas fa-images me-2"></i>Galerie <span class="badge bg-secondary">{{ $etablissement->medias->count() }}</span>
          </h6>
          @if ($etablissement->medias->count() > 0)
            <div class="gallery-container">
              <div class="thumbnail-scroll">
                <div class="d-flex flex-nowrap overflow-auto pb-2">
                  @foreach ($etablissement->medias as $media)
                    <div class="thumbnail-item me-2">
                      <img src="{{ asset('storage/'.$media->path) }}"
                           class="img-thumbnail thumbnail-image"
                           alt="Thumbnail {{ $loop->iteration }}"
                           style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;"
                           onclick="changeMainImage('{{ asset('storage/'.$media->path) }}')">
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          @else
            <div class="alert alert-light d-flex align-items-center">
              <i class="fas fa-image fa-2x me-3 text-muted"></i>
              <div>
                <h6 class="alert-heading mb-1">Aucune photo disponible</h6>
                <p class="mb-0 small">Ajoutez des photos pour améliorer la visibilité</p>
              </div>
            </div>
          @endif
        </div> --}}

                <!-- Section Adresse -->
                <div class="info-section mb-4">
                    <h6 class="section-title text-primary mb-3">
                        <i class="fas fa-map-marker-alt me-2"></i>Adresse
                    </h6>
                    <div class="address-card bg-light p-3 rounded">
                        <ul class="list-unstyled mb-0">
                            <li><strong><i class="fas fa-city me-2"></i>Ville :</strong> {{ $etablissement->ville }}
                            </li>
                            <li><strong><i class="fas fa-map-marked-alt me-2"></i>Commune :</strong>
                                {{ $etablissement->commune }}</li>
                            <li><strong><i class="fas fa-street-view me-2"></i>Quartier :</strong>
                                {{ $etablissement->quartier }}</li>
                            <li><strong><i class="fas fa-road me-2"></i>Avenue :</strong> {{ $etablissement->avenue }},
                                N°{{ $etablissement->numero }}</li>
                        </ul>
                    </div>
                </div>



            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>

            </div>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
    .info-section {
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
        border-bottom: 1px dashed #dee2e6;
    }

    .info-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .section-title {
        font-weight: 600;
        color: #0d6efd;
        position: relative;
        padding-left: 15px;
    }

    .section-title:before {
        content: "";
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        height: 70%;
        width: 4px;
        background-color: #0d6efd;
        border-radius: 2px;
    }

    .main-photo-container {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
    }

    .main-gallery-image {
        max-height: 300px;
        width: auto;
        max-width: 100%;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .thumbnail-scroll::-webkit-scrollbar {
        height: 5px;
    }

    .thumbnail-scroll::-webkit-scrollbar-thumb {
        background-color: #adb5bd;
        border-radius: 10px;
    }

    .thumbnail-image {
        transition: all 0.3s ease;
        opacity: 0.7;
    }

    .thumbnail-image:hover {
        opacity: 1;
        border-color: #0d6efd;
        transform: scale(1.05);
    }

    .address-card,
    .web-info {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }
</style>

<!-- Scripts -->
<script>
    // Changer l'image principale au clic sur les thumbnails
    function changeMainImage(fullImageUrl) {
        const mainImage = document.querySelector('.main-gallery-image');
        mainImage.src = fullImageUrl;
        mainImage.classList.add('animate__animated', 'animate__fadeIn');

        // Supprimer l'animation après qu'elle soit terminée
        setTimeout(() => {
            mainImage.classList.remove('animate__animated', 'animate__fadeIn');
        }, 1000);
    }

    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        // Animation au chargement
        const modalContent = document.querySelector('.modal-content');
        modalContent.style.opacity = '0';
        modalContent.style.transform = 'translateY(20px)';
        modalContent.style.transition = 'all 0.3s ease-out';

        setTimeout(() => {
            modalContent.style.opacity = '1';
            modalContent.style.transform = 'translateY(0)';
        }, 100);
    });
</script>
