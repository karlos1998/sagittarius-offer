<?php

namespace Database\Factories;

use App\Models\Ammunition;
use App\Models\Gun;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(5, 50);
        $pricePerShot = fake()->randomFloat(2, 2, 10);

        return [
            'order_id' => Order::factory(),
            'gun_id' => Gun::factory(),
            'ammunition_id' => Ammunition::factory(),
            'gun_name' => fake()->words(2, true),
            'ammunition_name' => fake()->words(2, true),
            'quantity' => $quantity,
            'price_per_shot' => $pricePerShot,
            'line_total' => round($quantity * $pricePerShot, 2),
        ];
    }
}
