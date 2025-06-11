<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TypeEtablissement>
 */
class TypeEtablissementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = [
            'Hôtel' => 'Établissement offrant des chambres et services hôteliers',
            'Restaurant' => 'Établissement proposant des repas et boissons',
            'Bar' => 'Établissement spécialisé dans les boissons',
            'Café' => 'Établissement proposant café et pâtisseries',
            'Club' => 'Établissement de divertissement nocturne',
            'Auberge' => 'Petit établissement hôtelier familial',
            'Guest House' => 'Maison d\'hôtes avec services personnalisés',
            'Lodge' => 'Hébergement en pleine nature',
            'Villa' => 'Location de villas de luxe',
            'Appartement' => 'Location d\'appartements meublés'
        ];

        $type = fake()->randomElement(array_keys($types));

        return [
            'nom' => $type,
            'description' => $types[$type],
        ];
    }
}
