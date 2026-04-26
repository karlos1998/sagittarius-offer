<?php

namespace Database\Factories;

use App\Models\Ammunition;
use App\Models\Caliber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ammunition>
 */
class AmmunitionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Amunicja '.fake()->bothify('##??'),
            'caliber_id' => Caliber::factory(),
            'club_price' => fake()->randomFloat(2, 1.5, 8.0),
            'standard_price' => fake()->randomFloat(2, 2.0, 10.0),
            'cart_quantity_step' => fake()->randomElement([5, 10, 15]),
        ];
    }
}
