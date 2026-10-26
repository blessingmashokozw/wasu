<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sender_id' => User::factory(),
            'receiver_id' => User::factory(),
            'order_id' => null,
            'listing_id' => null,
            'message_text' => fake()->text(2000),
            'is_read' => fake()->boolean(),
            'sent_at' => fake()->dateTime(),
        ];
    }
}
