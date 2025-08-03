<?php

namespace Database\Seeders;

use App\Models\Etablissement;
use App\Models\Photo;
use App\Models\Promotion;
use App\Models\Publicite;
use App\Models\Service;
use App\Models\TauxDeChange;
use App\Models\TypeEtablissement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Nettoyer le stockage des images
        Storage::deleteDirectory('public/photos');
        Storage::deleteDirectory('public/publicites');
        Storage::makeDirectory('public/photos');
        Storage::makeDirectory('public/publicites');
        
        TauxDeChange::updateOrCreate(
                ['date' => now()->format('Y-m-d')],
                ['usd_cdf' => rand(2500, 2800) + rand(0, 99)/100]
            );
        // Créer les utilisateurs
        $this->command->info('Création des utilisateurs...');
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'telephone' => fake()->phoneNumber(),
                'email_verified_at' => now(),
            ]
        );

        // $gerant = User::updateOrCreate(
        //     ['email' => 'gerant@example.com'],
        //     [
        //         'name' => 'Gérant',
        //         'password' => bcrypt('password'),
        //         'role' => 'etablissement',
        //         'telephone' => fake()->phoneNumber(),
        //         'email_verified_at' => now(),
        //     ]
        // );

        // User::factory()->count(5)->gerant()->create();
        // User::factory()->count(20)->create();

        // // Créer les types d'établissements
        // $this->command->info('Création des types d\'établissements...');
        // $typesList = [
        //     'Hôtel' => 'Établissement offrant des chambres et services hôteliers',
        //     'Restaurant' => 'Établissement proposant des repas et boissons',
        //     'Bar' => 'Établissement spécialisé dans les boissons',
        //     'Café' => 'Établissement proposant café et pâtisseries',
        //     'Club' => 'Établissement de divertissement nocturne',
        //     'Auberge' => 'Petit établissement hôtelier familial',
        //     'Guest House' => 'Maison d\'hôtes avec services personnalisés',
        //     'Lodge' => 'Hébergement en pleine nature',
        //     'Villa' => 'Location de villas de luxe',
        //     'Appartement' => 'Location d\'appartements meublés',
        // ];
        // $types = collect();
        // foreach ($typesList as $nom => $description) {
        //     $types->push(TypeEtablissement::updateOrCreate(
        //         ['nom' => $nom],
        //         ['description' => $description]
        //     ));
        // }

        // // Créer les établissements avec leurs relations
        // $this->command->info('Création des établissements...');
        // $etablissements = Etablissement::factory()
        //     ->count(15)
        //     ->sequence(fn($sequence) => [
        //         'type_etablissement_id' => $types->random()->id,

        //     ])
        //     ->create();





        

    //     // Pour chaque établissement, créer des services, photos, promotions et publicités
    //     $this->command->info('Création des services, photos, promotions et publicités...');
    //     foreach ($etablissements as $etablissement) {
    //         // Services
    //         Service::factory()
    //             ->count(rand(3, 8))
    //             ->create(['etablissement_id' => $etablissement->id]);

    //         // Photos
    //         Photo::factory()
    //             ->count(rand(5, 15))
    //             ->sequence(fn($sequence) => [
    //                 'etablissement_id' => $etablissement->id,
    //                 'est_principale' => $sequence->index === 0,
    //             ])
    //             ->create();

    //         // Promotions
    //         if (rand(0, 1)) {
    //             Promotion::factory()
    //                 ->count(rand(1, 3))
    //                 ->active()
    //                 ->create(['etablissement_id' => $etablissement->id]);

    //             Promotion::factory()
    //                 ->count(rand(1, 2))
    //                 ->expiree()
    //                 ->create(['etablissement_id' => $etablissement->id]);
    //         }

    //         // Publicités
    //         if (rand(0, 1)) {
    //             Publicite::factory()
    //                 ->count(rand(1, 2))
    //                 ->create(['etablissement_id' => $etablissement->id]);
    //         }
    //     }

    //     // Créer quelques publicités supplémentaires
    //     $this->command->info('Création de publicités supplémentaires...');
    //     Publicite::factory()
    //         ->count(5)
    //         ->create();

    //     $this->command->info('Base de données peuplée avec succès !');
    //     $this->command->info('Comptes de test :');
    //     $this->command->info('Admin - Email: admin@example.com / Mot de passe: password');
    //     $this->command->info('Gérant - Email: gerant@example.com / Mot de passe: password');
    // }
            }
}
