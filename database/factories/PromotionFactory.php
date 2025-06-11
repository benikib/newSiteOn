<?php

namespace Database\Factories;

use App\Models\Etablissement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion>
 */
class PromotionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $typesPromotions = [
            'Offre spéciale' => 'Réduction exceptionnelle sur nos services',
            'Pack week-end' => 'Forfait spécial week-end avec petit-déjeuner inclus',
            'Early Bird' => 'Réservez à l\'avance et bénéficiez d\'une réduction',
            'Last Minute' => 'Dernières chambres disponibles à prix réduit',
            'Séjour longue durée' => 'Réduction pour les séjours de plus de 7 nuits',
            'Fidélité' => 'Offre spéciale pour nos clients fidèles',
            'Saison' => 'Offre de saison avec services additionnels',
            'Anniversaire' => 'Célébrez votre anniversaire avec nous',
            'Événement' => 'Offre spéciale pour un événement particulier',
            'Groupe' => 'Réduction pour les réservations de groupe'
        ];

        $type = fake()->randomElement(array_keys($typesPromotions));
        $dateDebut = fake()->dateTimeBetween('now', '+1 month');
        $dateFin = fake()->dateTimeBetween($dateDebut, '+3 months');

        // Générer un code promo unique
        do {
            $codePromo = strtoupper(fake()->bothify('PROMO-####'));
        } while (\App\Models\Promotion::where('code_promo', $codePromo)->exists());

        return [
            'titre' => $type,
            'description' => $typesPromotions[$type],
            'etablissement_id' => Etablissement::factory(),
            'reduction' => fake()->randomFloat(2, 5, 50),
            'date_debut' => $dateDebut,
            'date_fin' => $dateFin,
            'conditions' => fake()->paragraph(),
            'code_promo' => $codePromo,
            'nombre_utilisations' => fake()->numberBetween(10, 1000),
            'nombre_utilisations_restantes' => fake()->numberBetween(0, 1000),
            'est_active' => true,
            'type_reduction' => fake()->randomElement(['pourcentage', 'montant_fixe']),
            'montant_minimum' => fake()->randomFloat(2, 50, 200),
            'montant_maximum' => fake()->randomFloat(2, 200, 1000),
            'jours_valides' => json_encode(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche']),
            'heures_valides' => json_encode([
                'debut' => '08:00',
                'fin' => '22:00'
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

    public function pourcentage(): static
    {
        return $this->state(fn(array $attributes) => [
            'type_reduction' => 'pourcentage',
            'reduction' => fake()->randomFloat(2, 5, 50),
        ]);
    }

    public function montantFixe(): static
    {
        return $this->state(fn(array $attributes) => [
            'type_reduction' => 'montant_fixe',
            'reduction' => fake()->randomFloat(2, 10, 100),
        ]);
    }
}
