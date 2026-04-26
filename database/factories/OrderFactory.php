<?php

namespace Database\Factories;

use App\Enums\OrderPaymentMethod;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'SO-'.now()->format('YmdHis').'-'.strtoupper(Str::random(5)),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'street' => fake()->streetName(),
            'house_number' => (string) fake()->numberBetween(1, 300),
            'apartment_number' => fake()->optional()->numberBetween(1, 80),
            'postal_code' => fake()->numerify('##-###'),
            'city' => fake()->city(),
            'email' => fake()->safeEmail(),
            'status' => OrderStatus::PendingVerification,
            'payment_method' => OrderPaymentMethod::PayOnSite,
            'payment_status' => OrderPaymentStatus::Pending,
            'is_club_member' => false,
            'is_completed' => false,
            'total_shots' => 10,
            'total_amount' => 100,
            'verification_code_hash' => fake()->sha256(),
            'verification_code_expires_at' => now()->addMinutes(5),
            'completed_at' => null,
            'download_token' => null,
        ];
    }
}
