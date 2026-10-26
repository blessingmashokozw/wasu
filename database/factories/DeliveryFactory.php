<?php

namespace Database\Factories;

use App\Models\Delivery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Delivery>
 */
class DeliveryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'delivery_method' => fake()->randomElement(['Standard Delivery', 'Express Delivery', 'Pickup']),
            'estimated_date' => fake()->date(),
            'current_status' => fake()->randomElement(['Processed', 'Dispatched', 'In transit', 'Delivered', 'Received']),
            'received_at' => fake()->optional()->dateTime(),
        ];
    }
}
