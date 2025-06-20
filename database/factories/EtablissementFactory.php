<?php

namespace Database\Factories;

use App\Models\TypeEtablissement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Etablissement>
 */
class EtablissementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $villes = ['Kinshasa', 'Lubumbashi', 'Matadi', 'Kisangani', 'Goma', 'Bukavu', 'Kolwezi', 'Mbuji-Mayi'];
        $communes = ['Gombe', 'Lingwala', 'Kalamu', 'Ngaliema', 'Limete', 'Masina', 'Ngaba', 'Bandalungwa'];
        $avenues = ['Victoire', 'Commerce', 'Industrielle', 'Principale', 'Libération', 'Indépendance', 'Révolution', 'Université'];

        return [
            'nom' => fake()->company(),
            'description' => fake()->paragraph(3),
            'telephone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'website' => fake()->url(),
            'ville' => fake()->randomElement($villes),
            'commune' => fake()->randomElement($communes),
            'avenue' => fake()->randomElement($avenues),
            'numero' => fake()->buildingNumber(),
            'type_etablissement_id' => TypeEtablissement::factory(),
            'statut' => fake()->randomElement(['actif', 'inactif', 'en_attente']),
            'note_moyenne' => fake()->randomFloat(1, 1, 5),
            'nombre_avis' => fake()->numberBetween(0, 100),
            'capacite' => fake()->numberBetween(10, 500),
            'horaires_ouverture' => '08:00',
            'horaires_fermeture' => '22:00',
            'jours_ouverture' => 'Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi',
            'services_additionnels' => json_encode([
                'wifi' => true,
                'parking' => true,
                'climatisation' => true,
                'restaurant' => fake()->boolean(),
                'piscine' => fake()->boolean(),
                'spa' => fake()->boolean(),
                'gym' => fake()->boolean(),
            ]),
        ];
    }

    public function actif(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut' => 'actif',
        ]);
    }

    public function inactif(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut' => 'inactif',
        ]);
    }

    public function enAttente(): static
    {
        return $this->state(fn(array $attributes) => [
            'statut' => 'en_attente',
        ]);
    }
}
