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
        $date = fake()->dateTimeBetween('now', '+1 month');

        return [
            'titre' => fake()->catchPhrase(),
            'description' => fake()->paragraph(),
            'etablissement_id' => Etablissement::factory(),
            'date' => $date,
            'dure' => fake()->numberBetween(1, 30), // Durée en jours
            'status' => fake()->randomElement(['actif', 'inactif', 'en attente']),
        ];
    }

    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'actif',
            'date' => now(),
            'dure' => fake()->numberBetween(15, 30),
        ]);
    }

    public function expiree(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'inactif',
            'date' => fake()->dateTimeBetween('-3 months', '-1 month'),
            'dure' => fake()->numberBetween(1, 7),
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
