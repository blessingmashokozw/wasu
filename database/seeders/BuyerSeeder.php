<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;


class BuyerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        for ($i = 1; $i <= 150; $i++) {
            User::factory()->buyer()->create([
                'full_name' => $faker->firstName." ".$faker->lastName ,
                'email' => $faker->email,
                'phone' => $faker->phoneNumber,
                'password' => bcrypt('password'),
            ]);
        }

        $this->command->info('=== Buyer Credentials ===');
        $this->command->info('Buyer 1 Email: buyer1@ims.test | Password: password');
        $this->command->info('Buyer 2 Email: buyer2@ims.test | Password: password');
        $this->command->info('Buyer 3 Email: buyer3@ims.test | Password: password');
    }
}
