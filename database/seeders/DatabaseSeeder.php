<?php

namespace Database\Seeders;

use App\Models\Etablissement;
use App\Models\Photo;
use App\Models\Promotion;
use App\Models\Publicite;
use App\Models\Service;
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

        // Créer les utilisateurs
        $this->command->info('Création des utilisateurs...');
        $admin = User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        $gerant = User::factory()->gerant()->create([
            'name' => 'Gérant',
            'email' => 'gerant@example.com',
            'password' => bcrypt('password'),
        ]);

        User::factory()->count(5)->gerant()->create();
        User::factory()->count(20)->create();

        // Créer les types d'établissements
        $this->command->info('Création des types d\'établissements...');
        $types = TypeEtablissement::factory()->count(10)->create();

        // Créer les établissements avec leurs relations
        $this->command->info('Création des établissements...');
        $etablissements = Etablissement::factory()
            ->count(15)
            ->sequence(fn($sequence) => [
                'type_etablissement_id' => $types->random()->id,
                'user_id' => User::where('role', 'gerant')->inRandomOrder()->first()->id,
            ])
            ->create();

        // Pour chaque établissement, créer des services, photos, promotions et publicités
        $this->command->info('Création des services, photos, promotions et publicités...');
        foreach ($etablissements as $etablissement) {
            // Services
            Service::factory()
                ->count(rand(3, 8))
                ->create(['etablissement_id' => $etablissement->id]);

            // Photos
            Photo::factory()
                ->count(rand(5, 15))
                ->sequence(fn($sequence) => [
                    'etablissement_id' => $etablissement->id,
                    'est_principale' => $sequence->index === 0,
                ])
                ->create();

            // Promotions
            if (rand(0, 1)) {
                Promotion::factory()
                    ->count(rand(1, 3))
                    ->active()
                    ->create(['etablissement_id' => $etablissement->id]);

                Promotion::factory()
                    ->count(rand(1, 2))
                    ->expiree()
                    ->create(['etablissement_id' => $etablissement->id]);
            }

            // Publicités
            if (rand(0, 1)) {
                Publicite::factory()
                    ->count(rand(1, 2))
                    ->active()
                    ->create(['etablissement_id' => $etablissement->id]);

                Publicite::factory()
                    ->count(rand(1, 2))
                    ->expiree()
                    ->create(['etablissement_id' => $etablissement->id]);
            }
        }

        // Créer quelques publicités supplémentaires
        $this->command->info('Création de publicités supplémentaires...');
        Publicite::factory()
            ->count(5)
            ->active()
            ->hautePriorite()
            ->create();

        $this->command->info('Base de données peuplée avec succès !');
        $this->command->info('Comptes de test :');
        $this->command->info('Admin - Email: admin@example.com / Mot de passe: password');
        $this->command->info('Gérant - Email: gerant@example.com / Mot de passe: password');
    }
}
