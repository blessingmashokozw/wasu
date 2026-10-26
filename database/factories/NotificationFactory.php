<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'notification_type' => fake()->randomElement(['order', 'payment', 'delivery', 'message', 'system']),
            'message' => fake()->sentence(),
            'is_read' => fake()->boolean(),
            'created_at' => fake()->dateTime(),
        ];
    }
}
