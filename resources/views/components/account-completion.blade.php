@auth
    @php
        $account = auth()->user()->loadMissing('usersEtablissements.etablissement', 'organisationPatronale');
        $missing = [];

        if (!$account->telephone) {
            $missing[] = 'téléphone du compte';
        }

        foreach ($account->usersEtablissements as $userEtablissement) {
            $etablissement = $userEtablissement->etablissement;
            if (!$etablissement) {
                continue;
            }

            foreach ([
                'telephone' => 'téléphone',
                'description' => 'description',
                'type_etablissement_id' => 'domaine d’activité',
                'ville' => 'ville',
                'commune' => 'commune',
                'quartier' => 'quartier',
            ] as $field => $label) {
                if (!$etablissement->{$field}) {
                    $missing[] = $label.' de '.$etablissement->nom;
                }
            }

            if ($etablissement->nature_entite === 'formalisee') {
                foreach ([
                    'rccm' => 'RCCM',
                    'id_nat' => 'Id Nat',
                    'nif' => 'NIF',
                    'cnss' => 'affiliation CNSS',
                    'inpp' => 'affiliation INPP',
                    'autorisation_path' => 'autorisation d’ouverture',
                ] as $field => $label) {
                    if (!$etablissement->{$field}) {
                        $missing[] = $label.' de '.$etablissement->nom;
                    }
                }
            }
        }

        if ($account->role === 'organisation_patronale') {
            $organisation = $account->organisationPatronale;
            if ($organisation) {
                foreach (['nom' => 'nom de l’organisation', 'telephone' => 'téléphone de l’organisation', 'adresse' => 'adresse de l’organisation'] as $field => $label) {
                    if (!$organisation->{$field}) {
                        $missing[] = $label;
                    }
                }
            }
        }

        $completionRoute = match ($account->role) {
            'organisation_patronale' => route('organisation.dashboard'),
            'etablissement' => route('dashboard_ets'),
            'admin' => route('dashboard'),
            default => route('profile.edit'),
        };
    @endphp

    @if (count($missing))
        <div class="alert alert-warning mx-3 mt-3 mb-0" role="status">
            <strong>Profil à compléter.</strong>
            Informations manquantes : {{ implode(', ', array_unique($missing)) }}.
            <a href="{{ $completionRoute }}" class="alert-link">Compléter mes informations</a>
        </div>
    @endif
@endauth
