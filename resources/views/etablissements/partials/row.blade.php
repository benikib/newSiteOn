<tr class="etablissementRow{{ $etablissement->id }}">
    <td>
        <div class="d-flex px-2 align-items-center">
            <div class="me-3">
                <img src="{{ asset('assets/img/small-logos/logo-spotify.svg') }}" class="avatar avatar-sm rounded-circle"
                    alt="logo">
            </div>
            <div>
                <h6 class="mb-0 text-sm">{{ $etablissement->nom }}</h6>
            </div>
        </div>
    </td>
    <td>
        <span class="badge bg-gradient-primary">{{ $etablissement->typeEtablissement->nom }}</span>
    </td>

    <td>
        <span class="text-xs font-weight-bold">{{ $etablissement->telephone }}</span>
    </td>

    <td class="align-middle text-center">
        <div class="btn-group" role="group">
            <!-- Bouton Détails -->
            <a href='{{ route('etablissements.show', $etablissement->id) }}' class="btn btn-info btn-sm px-2"
                data-bs-toggle="tooltip" title="Détails">
                <i class="fas fa-info-circle fa-fw"></i>
            </a>


            <!-- Bouton Modifier -->
            <button class="btn btn-warning btn-sm px-2" data-bs-toggle="modal"
                data-bs-target="#editEtablissementModal{{ $etablissement->id }}" data-bs-toggle="tooltip"
                title="Modifier">
                <i class="fas fa-edit fa-fw"></i>
            </button>

            <!-- Bouton Services -->
            <button class="btn btn-primary btn-sm px-2" data-bs-toggle="modal"
                data-bs-target="#serviceModal{{ $etablissement->id }}" data-bs-toggle="tooltip" title="Services">
                <i class="fas fa-concierge-bell fa-fw"></i>
            </button>

            <!-- Bouton Publicité -->
            <button class="btn btn-success btn-sm px-2" data-bs-toggle="modal"
                data-bs-target="#publiciteModal{{ $etablissement->id }}" data-bs-toggle="tooltip" title="Publicité">
                <i class="fas fa-ad fa-fw"></i>
            </button>

            <!-- Bouton Promotion -->
            <!-- Dans serviceModal, changez le bouton promotion -->
            <button class="btn btn-pink btn-sm px-2" data-bs-toggle="modal"
                data-bs-target="#servicePromotionModal{{ $etablissement->id }}" title="Promotions">
                <i class="fas fa-tag fa-fw"></i>
            </button>

            <!-- Bouton Galerie -->
            <button class="btn btn-secondary btn-sm px-2" data-bs-toggle="modal"
                data-bs-target="#galleryModal{{ $etablissement->id }}" data-bs-toggle="tooltip" title="Galerie">
                <i class="fas fa-images fa-fw"></i>
            </button>

            <!-- Bouton Supprimer -->
            <form action="{{ route('etablissements.destroy', $etablissement->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm px-2"
                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet établissement?')"
                    data-bs-toggle="tooltip" title="Supprimer">
                    <i class="fas fa-trash-alt fa-fw"></i>
                </button>
            </form>
        </div>
    </td>
</tr>

@include('etablissements.modals.show', ['etablissement' => $etablissement])
@include('etablissements.modals.edit', ['etablissement' => $etablissement])
@include('etablissements.modals.services', ['etablissement' => $etablissement])
@include('etablissements.modals.publicites', ['etablissement' => $etablissement])

@include('etablissements.partials.gallery-modal', ['etablissement' => $etablissement])
