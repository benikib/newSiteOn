@extends('layouts.public')

@section('title', 'Espace organisation patronale')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4">{{ $organisation->nom }}</h1>
    <p class="text-muted">Gestion des affiliations, cotisations et informations des membres.</p>

    <section class="card shadow-sm mb-4">
        <div class="card-header"><h2 class="h5 mb-0">Profil de l’organisation</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('organisation.profil.update') }}" class="row g-3">
                @csrf
                @method('PATCH')
                <div class="col-md-6">
                    <label class="form-label" for="organisation-nom">Nom de l’organisation</label>
                    <input class="form-control" id="organisation-nom" name="nom" value="{{ old('nom', $organisation->nom) }}" maxlength="255" required>
                    @error('nom')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="organisation-telephone">Téléphone</label>
                    <input class="form-control" id="organisation-telephone" name="telephone" value="{{ old('telephone', $organisation->telephone) }}" maxlength="25">
                    @error('telephone')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="organisation-email">E-mail de contact</label>
                    <input class="form-control" id="organisation-email" type="email" name="email" value="{{ old('email', $organisation->email) }}" maxlength="255">
                    @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="organisation-adresse">Adresse</label>
                    <input class="form-control" id="organisation-adresse" name="adresse" value="{{ old('adresse', $organisation->adresse) }}" maxlength="2000">
                    @error('adresse')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="col-12"><button class="btn btn-outline-primary">Enregistrer le profil</button></div>
            </form>
        </div>
    </section>

    <section class="card shadow-sm mb-4">
        <div class="card-header"><h2 class="h5 mb-0">Établissements affiliés</h2></div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Établissement</th><th>Domaine</th><th>Statut</th><th>Action</th></tr></thead>
                <tbody>
                @forelse ($etablissements as $etablissement)
                    <tr>
                        <td>{{ $etablissement->nom }}</td>
                        <td>{{ $etablissement->typeEtablissement?->nom ?? '—' }}</td>
                        <td>{{ str_replace('_', ' ', ucfirst($etablissement->affiliation_statut)) }}</td>
                        <td>
                            @if ($etablissement->affiliation_statut === 'en_attente')
                                @foreach (['approuvee' => 'Valider', 'refusee' => 'Refuser'] as $status => $label)
                                    <form class="d-inline" method="POST" action="{{ route('organisation.membres.affiliation', $etablissement) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="affiliation_statut" value="{{ $status }}">
                                        <button class="btn btn-sm {{ $status === 'approuvee' ? 'btn-success' : 'btn-outline-danger' }}">{{ $label }}</button>
                                    </form>
                                @endforeach
                            @endif
                            @if ($etablissement->affiliation_statut === 'approuvee')
                                <form class="d-inline" method="POST" action="{{ route('organisation.membres.destroy', $etablissement) }}" onsubmit="return confirm('Retirer cet établissement de vos membres ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Retirer</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">Aucune demande d’affiliation.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="card shadow-sm mb-4">
        <div class="card-header"><h2 class="h5 mb-0">Créer un appel de cotisation</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('organisation.cotisations.store') }}" class="row g-3">
                @csrf
                <div class="col-md-6"><label class="form-label">Membre</label>
                    <select class="form-select" name="etablissement_id" required>
                        <option value="">Choisir un membre validé</option>
                        @foreach ($etablissements->where('affiliation_statut', 'approuvee') as $etablissement)
                            <option value="{{ $etablissement->id }}">{{ $etablissement->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6"><label class="form-label">Intitulé</label><input class="form-control" name="libelle" required maxlength="150"></div>
                <div class="col-md-4"><label class="form-label">Montant</label><input class="form-control" type="number" name="montant" min="0.01" step="0.01" required></div>
                <div class="col-md-2"><label class="form-label">Devise</label><select class="form-select" name="devise"><option>CDF</option><option>USD</option></select></div>
                <div class="col-md-6"><label class="form-label">Échéance</label><input class="form-control" type="date" name="date_echeance" required></div>
                <div class="col-12"><label class="form-label">Détails</label><textarea class="form-control" name="description" rows="2"></textarea></div>
                <div class="col-12"><button class="btn btn-primary">Publier l’appel</button></div>
            </form>
        </div>
    </section>

    <section class="card shadow-sm mb-4">
        <div class="card-header"><h2 class="h5 mb-0">Suivi des cotisations et paiements</h2></div>
        <div class="card-body">
            @forelse ($cotisations as $cotisation)
                @php($paye = (float) $cotisation->paiements->sum('montant'))
                <article class="border rounded p-3 mb-3">
                    <div class="d-flex flex-wrap justify-content-between gap-2">
                        <div>
                            <strong>{{ $cotisation->libelle }}</strong> · {{ $cotisation->etablissement->nom }}
                            <div class="small text-muted">Échéance : {{ $cotisation->date_echeance->format('d/m/Y') }} · Statut : {{ ucfirst($cotisation->statut) }}</div>
                        </div>
                        <strong>{{ number_format((float) $cotisation->montant, 2, ',', ' ') }} {{ $cotisation->devise }} · payé : {{ number_format($paye, 2, ',', ' ') }}</strong>
                    </div>
                    @if ($paye < (float) $cotisation->montant)
                        <form method="POST" action="{{ route('organisation.cotisations.paiements.store', $cotisation) }}" class="row g-2 mt-2">
                            @csrf
                            <div class="col-md-3"><label class="form-label">Montant reçu</label><input class="form-control" type="number" name="montant" min="0.01" max="{{ (float) $cotisation->montant - $paye }}" step="0.01" required></div>
                            <div class="col-md-3"><label class="form-label">Date du paiement</label><input class="form-control" type="date" name="date_paiement" value="{{ today()->toDateString() }}" required></div>
                            <div class="col-md-3"><label class="form-label">Mode</label><input class="form-control" name="mode" maxlength="40"></div>
                            <div class="col-md-3 d-flex align-items-end"><button class="btn btn-outline-primary">Enregistrer le paiement</button></div>
                        </form>
                    @endif
                    @if ($cotisation->paiements->isNotEmpty())
                        <ul class="small mt-3 mb-0">
                            @foreach ($cotisation->paiements as $paiement)
                                <li>{{ $paiement->date_paiement->format('d/m/Y') }} : {{ number_format((float) $paiement->montant, 2, ',', ' ') }} {{ $cotisation->devise }} {{ $paiement->reference ? '· '.$paiement->reference : '' }}</li>
                            @endforeach
                        </ul>
                    @endif
                </article>
            @empty
                <p class="text-muted mb-0">Aucune cotisation enregistrée.</p>
            @endforelse
        </div>
    </section>

    <section class="card shadow-sm">
        <div class="card-header"><h2 class="h5 mb-0">Publier une information</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('organisation.publications.store') }}" class="row g-3">
                @csrf
                <div class="col-md-6"><label class="form-label">Titre</label><input class="form-control" name="titre" required maxlength="200"></div>
                <div class="col-md-6"><label class="form-label">Cible</label><select class="form-select" name="cible" id="publication-cible"><option value="tous">Tous les utilisateurs</option><option value="domaines">Domaines d’activité sélectionnés</option></select></div>
                <div class="col-md-6"><label class="form-label">Catégorie</label><select class="form-select" name="categorie" required><option value="News">News</option><option value="Vulgarisation">Vulgarisation</option><option value="Rappel">Rappel</option></select></div>
                <div class="col-12"><label class="form-label">Synopsis</label><input class="form-control" name="synopsis" maxlength="500"></div>
                <div class="col-12"><label class="form-label">Contenu</label><textarea class="form-control" name="contenu" rows="4" required></textarea></div>
                <div class="col-md-6"><label class="form-label">Ville (facultatif)</label><input class="form-control" name="ville" maxlength="255"></div>
                <div class="col-md-6"><label class="form-label">Commune (facultatif)</label><input class="form-control" name="commune" maxlength="255"></div>
                <div class="col-md-6"><label class="form-label">Publication à partir de</label><input class="form-control" type="datetime-local" name="publie_le"></div>
                <div class="col-md-6"><label class="form-label">Expiration (facultative)</label><input class="form-control" type="datetime-local" name="expire_le"></div>
                <fieldset class="col-12">
                    <legend class="form-label">Domaines d’activité (types d’établissement)</legend>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach ($typesEtablissement as $type)
                            <label class="form-check"><input class="form-check-input" type="checkbox" name="types_etablissement[]" value="{{ $type->id }}"><span class="form-check-label">{{ $type->nom }}</span></label>
                        @endforeach
                    </div>
                    <small class="text-muted">Ces choix sont requis si la cible est limitée aux domaines sélectionnés.</small>
                </fieldset>
                <div class="col-12"><button class="btn btn-primary">Publier</button></div>
            </form>
        </div>
    </section>
</div>
@endsection
