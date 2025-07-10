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
            <a href='{{ route('etablissements.show', $etablissement->id) }}' class="btn btn-sm btn-outline-primary"
                data-bs-toggle="tooltip" title="Détails">
                <i class="fas fa-edit"></i>
            </a>


            <!-- Bouton de suppression -->
            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                data-bs-target="#modalDelete{{ $etablissement->id }}" title="Supprimer">
                <i class="bi bi-trash"></i>
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
