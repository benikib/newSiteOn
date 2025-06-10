<?php

namespace Database\Factories;

use App\Models\Etablissement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Photo>
 */
class PhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $typesPhotos = [
            'Façade' => 'Vue extérieure de l\'établissement',
            'Chambre' => 'Vue d\'une chambre',
            'Suite' => 'Vue d\'une suite',
            'Restaurant' => 'Vue du restaurant',
            'Bar' => 'Vue du bar',
            'Piscine' => 'Vue de la piscine',
            'Spa' => 'Vue du spa',
            'Salle de sport' => 'Vue de la salle de sport',
            'Salle de conférence' => 'Vue d\'une salle de conférence',
            'Jardin' => 'Vue du jardin',
            'Terrasse' => 'Vue de la terrasse',
            'Vue panoramique' => 'Vue panoramique depuis l\'établissement'
        ];

        $type = fake()->randomElement(array_keys($typesPhotos));

        return [
            'titre' => $type,
            'description' => $typesPhotos[$type],
            'image_path' => 'public/photos/' . fake()->image('public/storage/photos', 800, 600, null, false),
            'etablissement_id' => Etablissement::factory(),
            'ordre' => fake()->numberBetween(1, 20),
            'est_principale' => fake()->boolean(20),
            'est_publique' => true,
            'date_prise' => fake()->dateTimeBetween('-1 year', 'now'),
            'dimensions' => json_encode([
                'largeur' => 800,
                'hauteur' => 600
            ]),
            'taille' => fake()->numberBetween(100, 2000) . 'KB',
            'format' => 'jpg',
        ];
    }

    public function principale(): static
    {
        return $this->state(fn(array $attributes) => [
            'est_principale' => true,
            'ordre' => 1,
        ]);
    }

    public function privee(): static
    {
        return $this->state(fn(array $attributes) => [
            'est_publique' => false,
        ]);
    }
}
