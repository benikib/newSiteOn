<?php

namespace App\Services;

use App\Models\Etablissement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AccountRegistration
{
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $typeCompte = $data['type_compte'] ?? 'client';
            $role = match ($typeCompte) {
                'organisation_patronale' => 'organisation_patronale',
                'entite_formalisee', 'entite_non_formalisee' => 'etablissement',
                default => 'client',
            };
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'telephone' => $data['telephone'] ?? null,
                'role' => $role,
                'is_approved' => false,
            ]);

            if ($role === 'organisation_patronale') {
                $user->organisationPatronale()->create([
                    'nom' => $data['nom_entite'],
                    'telephone' => $data['telephone'] ?? null,
                    'email' => $data['email'],
                    'adresse' => implode(', ', array_filter([
                        $data['ville'] ?? null,
                        $data['commune'] ?? null,
                        $data['quartier'] ?? null,
                    ])) ?: null,
                ]);
            } elseif ($role === 'etablissement') {
                $etablissement = Etablissement::create([
                    'nom' => $data['nom_entite'],
                    'telephone' => $data['telephone'],
                    'type_etablissement_id' => $data['type_etablissement_id'],
                    'nature_entite' => $typeCompte === 'entite_formalisee' ? 'formalisee' : 'clandestine',
                    'ville' => $data['ville'] ?? null,
                    'commune' => $data['commune'] ?? null,
                    'quartier' => $data['quartier'] ?? null,
                    'avenue' => $data['avenue'] ?? null,
                    'numero' => $data['numero'] ?? null,
                    'statut' => 'en_attente',
                ]);

                $user->usersEtablissements()->create([
                    'etablissement_id' => $etablissement->id,
                ]);
            }

            return $user;
        });
    }
}
