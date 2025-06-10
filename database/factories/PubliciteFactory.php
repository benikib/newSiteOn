<?php

namespace Database\Factories;

use App\Models\Etablissement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Publicite>
 */
class PubliciteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $typesPublicites = [
            'Bannière principale' => 'Publicité en haut de page',
            'Carrousel' => 'Publicité dans le carrousel d\'images',
            'Sidebar' => 'Publicité dans la barre latérale',
            'Pop-up' => 'Publicité pop-up',
            'Newsletter' => 'Publicité dans la newsletter',
            'Email' => 'Publicité par email',
            'Réseaux sociaux' => 'Publicité sur les réseaux sociaux',
            'Recherche' => 'Publicité dans les résultats de recherche',
            'Détail établissement' => 'Publicité sur la page de détail',
            'Confirmation' => 'Publicité sur la page de confirmation'
        ];

        $type = fake()->randomElement(array_keys($typesPublicites));
        $dateDebut = fake()->dateTimeBetween('now', '+1 month');
        $dateFin = fake()->dateTimeBetween($dateDebut, '+3 months');

        return [
            'titre' => fake()->catchPhrase(),
            'description' => fake()->paragraph(),
            'etablissement_id' => Etablissement::factory(),
            'type' => $type,
            'position' => fake()->randomElement(['haut', 'milieu', 'bas', 'gauche', 'droite']),
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'image_path' => 'public/publicites/' . fake()->image('public/storage/publicites', 1200, 400, null, false),
            'lien' => fake()->url(),
            'est_active' => true,
            'priorite' => fake()->numberBetween(1, 10),
            'nombre_clics' => fake()->numberBetween(0, 1000),
            'nombre_impressions' => fake()->numberBetween(1000, 10000),
            'budget' => fake()->randomFloat(2, 100, 1000),
            'cout_par_clic' => fake()->randomFloat(2, 0.1, 2),
            'cout_par_impression' => fake()->randomFloat(2, 0.01, 0.1),
            'cible' => json_encode([
                'age_min' => fake()->numberBetween(18, 30),
                'age_max' => fake()->numberBetween(31, 65),
                'genres' => ['homme', 'femme'],
                'interets' => ['voyage', 'gastronomie', 'loisirs', 'bien-être']
            ]),
            'statistiques' => json_encode([
                'taux_clic' => fake()->randomFloat(2, 0.1, 5),
                'taux_conversion' => fake()->randomFloat(2, 0.1, 3),
                'roi' => fake()->randomFloat(2, 100, 500)
            ]),
        ];
    }

    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'est_active' => true,
            'date_debut' => now(),
            'date_fin' => fake()->dateTimeBetween('+1 month', '+3 months'),
        ]);
    }

    public function expiree(): static
    {
        return $this->state(fn(array $attributes) => [
            'est_active' => false,
            'date_debut' => fake()->dateTimeBetween('-3 months', '-1 month'),
            'date_fin' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function hautePriorite(): static
    {
        return $this->state(fn(array $attributes) => [
            'priorite' => fake()->numberBetween(8, 10),
        ]);
    }

    public function bassePriorite(): static
    {
        return $this->state(fn(array $attributes) => [
            'priorite' => fake()->numberBetween(1, 3),
        ]);
    }
}
