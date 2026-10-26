<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use App\Models\Listing;
use App\Models\Order;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Get sellers and buyers
        $sellers = User::where('role', 'Seller')->get();
        $buyers = User::where('role', 'Buyer')->get();

        if ($sellers->isEmpty() || $buyers->isEmpty()) {
            $this->command->warn('No sellers or buyers found. Please run SellerSeeder and BuyerSeeder first.');
            return;
        }

        // Get listings and orders
        $listings = Listing::all();
        $orders = Order::all();

        // Sample message templates
        $buyerMessages = [
            "Is this item still available?",
            "Can you provide more details about the product?",
            "What's the condition of this item?",
            "Are you open to negotiation on the price?",
            "How soon can you deliver?",
            "Do you offer bulk discounts?",
            "Is this the exact item in the photos?",
            "What payment methods do you accept?",
            "Can I see more photos?",
            "Is this brand new or used?",
        ];

        $sellerMessages = [
            "Yes, it's still available!",
            "The item is in excellent condition.",
            "I can deliver within 2-3 business days.",
            "I can offer a 5% discount for bulk orders.",
            "These are actual photos of the item.",
            "I accept all major payment methods.",
            "The item is brand new with original packaging.",
            "I've uploaded more photos for you.",
            "Let me know if you have any other questions.",
            "Thank you for your interest!",
        ];

        // Create conversations between buyers and sellers
        foreach ($sellers as $seller) {
            // Get listings for this seller
            $sellerListings = $listings->where('seller_id', $seller->user_id);

            // Skip if seller has no listings
            if ($sellerListings->isEmpty()) {
                continue;
            }

            // Each seller has conversations with 2-3 random buyers
            $conversationBuyers = $buyers->random(min(rand(2, 3), $buyers->count()));

            foreach ($conversationBuyers as $buyer) {
                // Get a random listing from this seller
                $listing = $sellerListings->random();

                // Get a random order between this buyer and seller (if exists)
                $order = $orders->where('buyer_id', $buyer->user_id)->where('seller_id', $seller->user_id)->first();

                // Create 3-8 messages per conversation
                $messageCount = rand(3, 8);
                $isBuyerTurn = rand(0, 1) === 0; // Randomly decide who starts

                for ($i = 0; $i < $messageCount; $i++) {
                    $sender = $isBuyerTurn ? $buyer : $seller;
                    $receiver = $isBuyerTurn ? $seller : $buyer;
                    $messagePool = $isBuyerTurn ? $buyerMessages : $sellerMessages;

                    Message::create([
                        'sender_id' => $sender->user_id,
                        'receiver_id' => $receiver->user_id,
                        'order_id' => $order ? $order->order_id : null,
                        'listing_id' => $listing->listing_id,
                        'message_text' => $faker->randomElement($messagePool),
                        'is_read' => $faker->boolean(70), // 70% chance of being read
                        'sent_at' => $faker->dateTimeBetween('-30 days', 'now'),
                    ]);

                    // Alternate turns
                    $isBuyerTurn = !$isBuyerTurn;
                }
            }
        }

        $this->command->info('Messages seeded successfully.');
    }
}
