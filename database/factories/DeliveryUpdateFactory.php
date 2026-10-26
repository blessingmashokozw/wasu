<?php

namespace Database\Factories;

use App\Models\DeliveryUpdate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryUpdate>
 */
class DeliveryUpdateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'updated_by' => User::factory(),
            'status' => fake()->randomElement(['Processed', 'Dispatched', 'In transit', 'Delivered']),
            'note' => fake()->optional()->sentence(),
            'updated_at' => fake()->dateTime(),
        ];
    }
}
