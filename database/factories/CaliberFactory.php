<?php

namespace Database\Factories;

use App\Models\Caliber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Caliber>
 */
class CaliberFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                '9x19',
                '5.56 NATO',
                '.45 ACP',
                '.22 LR',
            ]).'-'.fake()->unique()->numberBetween(1, 999),
        ];
    }
}
