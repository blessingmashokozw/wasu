<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class SellerSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        // 3 Verified Sellers
        for ($i = 1; $i <= 5; $i++) {
            $seller = User::factory()->verifiedSeller()->create([
                'full_name' => $faker->firstName . " " . $faker->lastName,
                'email' => $faker->email,
                'phone' => $faker->phoneNumber,
                'password' => bcrypt('password'),
            ]);

            // Create 10+ listings for each verified seller
            $listingCount = rand(10, 15);
            for ($j = 1; $j <= $listingCount; $j++) {
                $listing = Listing::factory()->create([
                    'seller_id' => $seller->user_id,
                    // 'title' => "Listing {$j} by Seller {$i}",
                ]);

                // Add at least one image per listing
                ListingImage::factory()->primary()->create([
                    'listing_id' => $listing->listing_id,
                ]);

                // Add 1-2 additional images
                if (rand(0, 1)) {
                    ListingImage::factory()->create(['listing_id' => $listing->listing_id]);
                }
            }
        }

        // 1 Pending Seller
        // for ($i = 1; $i <= 15; $i++) {
            User::factory()->seller()->create([
                'full_name' => $faker->firstName . " " . $faker->lastName,
                'email' => $faker->email,
                'phone' => $faker->phoneNumber,
                'password' => bcrypt('password'),
            ]);
        // }

        $this->command->info('=== Seller Credentials ===');
        $this->command->info('Verified Seller 1 Email: seller1@ims.test | Password: password');
        $this->command->info('Verified Seller 2 Email: seller2@ims.test | Password: password');
        $this->command->info('Verified Seller 3 Email: seller3@ims.test | Password: password');
        $this->command->info('Pending Seller Email: pending.seller@ims.test | Password: password');
    }
}
