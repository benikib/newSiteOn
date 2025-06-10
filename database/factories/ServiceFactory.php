<?php

namespace Database\Factories;

use App\Models\Etablissement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $typesServices = [
            'Chambre Standard' => 'Chambre confortable avec lit double et salle de bain privée',
            'Chambre Deluxe' => 'Chambre spacieuse avec vue panoramique et services premium',
            'Suite' => 'Suite luxueuse avec salon séparé et services VIP',
            'Table d\'hôte' => 'Menu du jour avec entrée, plat et dessert',
            'Menu dégustation' => 'Dégustation de 5 plats signature du chef',
            'Massage relaxant' => 'Massage corporel de 60 minutes',
            'Soin du visage' => 'Soin complet du visage avec produits bio',
            'Accès piscine' => 'Accès à la piscine avec serviette et parasol',
            'Cours de fitness' => 'Cours collectif de 45 minutes',
            'Location de salle' => 'Location de salle pour événements privés'
        ];

        $service = fake()->unique()->randomElement(array_keys($typesServices));

        return [
            'nom' => $service,
            'description' => $typesServices[$service],
            'prix' => fake()->randomFloat(2, 20, 500),
            'duree' => fake()->randomElement(['1h', '2h', '3h', '4h', 'journée', 'nuitée']),
            'disponibilite' => fake()->boolean(80),
            'etablissement_id' => Etablissement::factory(),
            'categorie' => fake()->randomElement(['hébergement', 'restauration', 'bien-être', 'loisirs', 'événementiel']),
            'promotion' => fake()->boolean(20) ? fake()->randomFloat(2, 5, 30) : null,
            'date_debut_promo' => fake()->boolean(20) ? fake()->dateTimeBetween('now', '+1 month') : null,
            'date_fin_promo' => fake()->boolean(20) ? fake()->dateTimeBetween('+1 month', '+2 months') : null,
        ];
    }

    public function enPromotion(): static
    {
        return $this->state(fn(array $attributes) => [
            'promotion' => fake()->randomFloat(2, 5, 30),
            'date_debut_promo' => now(),
            'date_fin_promo' => fake()->dateTimeBetween('+1 month', '+2 months'),
        ]);
    }

    public function disponible(): static
    {
        return $this->state(fn(array $attributes) => [
            'disponibilite' => true,
        ]);
    }

    public function indisponible(): static
    {
        return $this->state(fn(array $attributes) => [
            'disponibilite' => false,
        ]);
    }
}
