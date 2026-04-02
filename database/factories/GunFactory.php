<?php

namespace Database\Factories;

use App\Models\Caliber;
use App\Models\Gun;
use App\Models\GunType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gun>
 */
class GunFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Model '.fake()->bothify('??-###'),
            'gun_type_id' => GunType::factory(),
            'caliber_id' => Caliber::factory(),
            'description' => fake()->sentence(),
            'photos' => [],
        ];
    }
}
