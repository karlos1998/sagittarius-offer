<?php

namespace Database\Factories;

use App\Models\GunType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GunType>
 */
class GunTypeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Pistolet',
                'Karabin',
                'Strzelba',
            ]).'-'.fake()->unique()->numberBetween(1, 999),
        ];
    }
}
