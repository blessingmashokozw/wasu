<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Delivery;
use App\Models\User;
use App\Models\Listing;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class OrderSeeder extends Seeder
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

        // Get listings
        $listings = Listing::where('listing_status', 'Active')->get();

        if ($listings->isEmpty()) {
            $this->command->warn('No listings found. Please ensure sellers have listings.');
            return;
        }

        // Order statuses (must match database enum)
        $orderStatuses = ['Pending payment', 'Paid', 'Completed', 'Cancelled'];
        $deliveryMethods = ['Standard Delivery', 'Express Delivery', 'Pickup'];
        $paymentMethods = ['Paynow', 'EcoCash', 'Bank Transfer', 'Cash on Delivery'];

        // Create orders for each buyer
        foreach ($buyers as $buyer) {
            // Each buyer places 2-5 orders
            $orderCount = rand(2, 5);

            for ($i = 0; $i < $orderCount; $i++) {
                // Select a random seller
                $seller = $sellers->random();

                // Select 1-3 listings from this seller
                $sellerListings = $listings->where('seller_id', $seller->user_id);
                $selectedListings = $sellerListings->random(min(rand(1, 3), $sellerListings->count()));

                // Calculate total and create order items
                $totalAmount = 0;
                $orderItemsData = [];

                foreach ($selectedListings as $listing) {
                    $quantity = rand(1, min(3, $listing->quantity_available));
                    $unitPrice = $listing->price;
                    $subtotal = $quantity * $unitPrice;
                    $totalAmount += $subtotal;

                    $orderItemsData[] = [
                        'listing_id' => $listing->listing_id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'subtotal' => $subtotal,
                    ];
                }

                // Get buyer's delivery address
                $buyerAddress = $buyer->buyer->delivery_address ?? '123 Main Street, Harare, Zimbabwe';

                // Determine order status (weighted towards completed orders)
                $statusWeights = [
                    'Pending payment' => 10,
                    'Paid' => 15,
                    'Processing' => 20,
                    'Shipped' => 25,
                    'Delivered' => 25,
                    'Cancelled' => 5,
                ];
                $orderStatus = $faker->randomElement($orderStatuses);

                // Create order
                $order = Order::create([
                    'buyer_id' => $buyer->user_id,
                    'seller_id' => $seller->user_id,
                    'order_date' => $faker->dateTimeBetween('-60 days', 'now'),
                    'total_amount' => $totalAmount,
                    'delivery_address' => $buyerAddress,
                    'order_status' => $orderStatus,
                ]);

                // Create order items
                foreach ($orderItemsData as $itemData) {
                    OrderItem::create([
                        'order_id' => $order->order_id,
                        'listing_id' => $itemData['listing_id'],
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $itemData['unit_price'],
                        'subtotal' => $itemData['subtotal'],
                    ]);
                }

                // Create payment (unless cancelled)
                if ($orderStatus !== 'Pending payment' && $orderStatus !== 'Cancelled') {
                    $paymentStatus = 'Successful';
                    $paidAt = Carbon::parse($order->order_date)->addDays(rand(1, 3));

                    Payment::create([
                        'order_id' => $order->order_id,
                        'payment_method' => $faker->randomElement($paymentMethods),
                        'amount' => $totalAmount,
                        'transaction_ref' => 'TXN-' . strtoupper($faker->unique()->bothify('????????')),
                        'payment_status' => $paymentStatus,
                        'paid_at' => $paidAt,
                    ]);
                } elseif ($orderStatus === 'Pending payment') {
                    Payment::create([
                        'order_id' => $order->order_id,
                        'payment_method' => $faker->randomElement($paymentMethods),
                        'amount' => $totalAmount,
                        'transaction_ref' => 'TXN-' . strtoupper($faker->unique()->bothify('????????')),
                        'payment_status' => 'Initiated',
                        'paid_at' => null,
                    ]);
                }

                // Create delivery (for paid orders)
                if ($orderStatus !== 'Pending payment' && $orderStatus !== 'Cancelled') {
                    $deliveryStatus = match ($orderStatus) {
                        'Paid' => 'Processed',
                        'Completed' => 'Received',
                        default => 'Processed',
                    };

                    $estimatedDate = Carbon::parse($order->order_date)->addDays(rand(5, 14));
                    $receivedAt = $orderStatus === 'Completed' ? Carbon::parse($estimatedDate)->addDays(rand(1, 3)) : null;

                    Delivery::create([
                        'order_id' => $order->order_id,
                        'delivery_method' => $faker->randomElement($deliveryMethods),
                        'estimated_date' => $estimatedDate,
                        'current_status' => $deliveryStatus,
                        'received_at' => $receivedAt,
                    ]);
                }
            }
        }

        $this->command->info('Orders seeded successfully.');
    }
}
