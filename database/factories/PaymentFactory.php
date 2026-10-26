<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payment_method' => fake()->randomElement(['Paynow', 'Credit Card', 'Bank Transfer']),
            'amount' => fake()->randomFloat(2, 10, 1000),
            'transaction_ref' => fake()->unique()->uuid(),
            'payment_status' => fake()->randomElement(['Initiated', 'Successful', 'Failed']),
            'paid_at' => fake()->optional()->dateTime(),
        ];
    }

    public function successful(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'Successful',
            'paid_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'Failed',
            'paid_at' => null,
        ]);
    }
}
