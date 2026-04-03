<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GunPackage>
 */
class GunPackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Pakiet '.fake()->words(2, true),
            'description' => fake()->sentence(),
            'preview_image' => null,
            'is_active' => true,
        ];
    }
}
