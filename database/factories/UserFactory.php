<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->phoneNumber(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => fake()->randomElement(['Buyer', 'Seller', 'Administrator']),
            'account_status' => 'Active',
            'failed_attempts' => 0,
            'locked_until' => null,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function buyer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'Buyer',
            'account_status' => 'Active',
        ])->afterCreating(function (\App\Models\User $user) {
            \App\Models\Buyer::create([
                'user_id' => $user->user_id,
                'delivery_address' => fake()->address(),
                'city' => fake()->city(),
            ]);
        });
    }

    public function seller(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'Seller',
            'account_status' => 'Pending',
        ])->afterCreating(function (\App\Models\User $user) {
            \App\Models\Seller::create([
                'user_id' => $user->user_id,
                'business_name' => fake()->company(),
                'business_reg_no' => fake()->optional()->numerify('REG########'),
                'business_address' => fake()->address(),
                'verification_status' => 'Pending',
            ]);
        });
    }

    public function verifiedSeller(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'Seller',
            'account_status' => 'Active',
        ])->afterCreating(function (\App\Models\User $user) {
            $admin = \App\Models\User::where('role', 'Administrator')->first();
            \App\Models\Seller::create([
                'user_id' => $user->user_id,
                'business_name' => fake()->company(),
                'business_reg_no' => fake()->optional()->numerify('REG########'),
                'business_address' => fake()->address(),
                'verification_status' => 'Verified',
                'verified_by' => $admin?->user_id,
                'verified_at' => now(),
            ]);
        });
    }

    public function administrator(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'Administrator',
            'account_status' => 'Active',
        ])->afterCreating(function (\App\Models\User $user) {
            \App\Models\Administrator::create([
                'user_id' => $user->user_id,
                'admin_level' => 'Standard',
            ]);
        });
    }

    public function superAdministrator(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'Administrator',
            'account_status' => 'Active',
        ])->afterCreating(function (\App\Models\User $user) {
            \App\Models\Administrator::create([
                'user_id' => $user->user_id,
                'admin_level' => 'Super',
            ]);
        });
    }
}
