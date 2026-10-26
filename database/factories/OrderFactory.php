<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $buyer = User::factory()->buyer()->create();
        $seller = User::factory()->seller()->create();

        return [
            'buyer_id' => $buyer->user_id,
            'seller_id' => $seller->user_id,
            'order_date' => fake()->date(),
            'total_amount' => fake()->randomFloat(2, 10, 1000),
            'delivery_address' => fake()->address(),
            'order_status' => fake()->randomElement(['Pending payment', 'Paid', 'Completed', 'Cancelled']),
        ];
    }
}
