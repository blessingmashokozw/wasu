<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Delivery;
use App\Models\DeliveryUpdate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Seller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    /**
     * Display a listing of the seller's orders with deliveries.
     */
    public function index()
    {
        //for sellers
        $data = Seller::with('user','listings.category','orders.orderItems','orders.delivery','orders.payments','orders.buyer')
        ->where('user_id',auth()->id())->get();

        // Calculate aggregated statistics
        $seller = $data->first();
        if (!$seller) {
            return Inertia::render('Seller/Reports/Index', [
                'summary' => [
                    'total_listings' => 0,
                    'total_orders' => 0,
                    'total_revenue' => 0,
                    'successful_orders' => 0,
                    'cancelled_orders' => 0,
                    'pending_orders' => 0,
                ],
                'monthly_revenue' => [],
                'category_revenue' => [],
                'top_listings' => [],
                'order_status_distribution' => [],
                'payment_method_distribution' => [],
                'data' => $data,
            ]);
        }

        $listings = $seller->listings;
        $orders = $seller->orders;
        $totalRevenue = 0;
        $monthlyRevenue = [];
        $categoryRevenue = [];
        $listingRevenue = [];
        $orderStatusDistribution = [
            'Completed' => 0,
            'Paid' => 0,
            'Pending payment' => 0,
            'Cancelled' => 0,
        ];
        $paymentMethodDistribution = [];

        foreach ($orders as $order) {
            $orderAmount = (float) $order->total_amount;
            $totalRevenue += $orderAmount;

            // Order status distribution
            if (isset($orderStatusDistribution[$order->order_status])) {
                $orderStatusDistribution[$order->order_status]++;
            }

            // Monthly revenue
            $month = date('Y-m', strtotime($order->order_date));
            if (!isset($monthlyRevenue[$month])) {
                $monthlyRevenue[$month] = 0;
            }
            $monthlyRevenue[$month] += $orderAmount;

            // Payment method distribution
            if ($order->payments) {
                foreach ($order->payments as $payment) {
                    $method = $payment->payment_method ?? 'Unknown';
                    if (!isset($paymentMethodDistribution[$method])) {
                        $paymentMethodDistribution[$method] = 0;
                    }
                    $paymentMethodDistribution[$method] += (float) $payment->amount;
                }
            }

            // Listing revenue
            if ($order->order_items) {
                foreach ($order->order_items as $item) {
                    // Find listing title from seller's listings
                    $listing = $listings->firstWhere('listing_id', $item->listing_id);
                    $listingTitle = $listing ? $listing->title : 'Unknown';
                    if (!isset($listingRevenue[$listingTitle])) {
                        $listingRevenue[$listingTitle] = 0;
                    }
                    $listingRevenue[$listingTitle] += (float) $item->subtotal;
                }
            }
        }

        // Category revenue from listings
        foreach ($listings as $listing) {
            $category = isset($listing->category) ? ($listing->category->category_name ?? 'Unknown') : 'Unknown';
            if (!isset($categoryRevenue[$category])) {
                $categoryRevenue[$category] = 0;
            }
            // Calculate revenue from orders for this listing
            foreach ($orders as $order) {
                if ($order->order_items) {
                    foreach ($order->order_items as $item) {
                        if ($item->listing_id == $listing->listing_id) {
                            $categoryRevenue[$category] += (float) $item->subtotal;
                        }
                    }
                }
            }
        }

        // Convert monthly revenue to sorted array
        ksort($monthlyRevenue);
        $monthlyRevenueArray = [];
        foreach ($monthlyRevenue as $month => $amount) {
            $monthlyRevenueArray[] = [
                'month' => $month,
                'amount' => round($amount, 2),
            ];
        }

        // Convert category revenue to array
        $categoryRevenueArray = [];
        foreach ($categoryRevenue as $category => $amount) {
            $categoryRevenueArray[] = [
                'category' => $category,
                'amount' => round($amount, 2),
            ];
        }

        // Convert listing revenue to array and sort by amount
        uasort($listingRevenue, function($a, $b) {
            return $b <=> $a;
        });
        $topListingsArray = [];
        $count = 0;
        foreach ($listingRevenue as $listing => $amount) {
            if ($count < 10) { // Top 10 listings
                $topListingsArray[] = [
                    'listing' => $listing,
                    'amount' => round($amount, 2),
                ];
                $count++;
            }
        }

        // Convert order status distribution to array
        $orderStatusArray = [];
        foreach ($orderStatusDistribution as $status => $count) {
            $orderStatusArray[] = [
                'status' => $status,
                'count' => $count,
            ];
        }

        // Convert payment method distribution to array
        $paymentMethodArray = [];
        foreach ($paymentMethodDistribution as $method => $amount) {
            $paymentMethodArray[] = [
                'method' => $method,
                'amount' => round($amount, 2),
            ];
        }

        return Inertia::render('Seller/Reports/Index', [
            'summary' => [
                'total_listings' => $listings->count(),
                'total_orders' => $orders->count(),
                'total_revenue' => round($totalRevenue, 2),
                'successful_orders' => $orderStatusDistribution['Completed'] + $orderStatusDistribution['Paid'],
                'cancelled_orders' => $orderStatusDistribution['Cancelled'],
                'pending_orders' => $orderStatusDistribution['Pending payment'],
            ],
            'monthly_revenue' => $monthlyRevenueArray,
            'category_revenue' => $categoryRevenueArray,
            'top_listings' => $topListingsArray,
            'order_status_distribution' => $orderStatusArray,
            'payment_method_distribution' => $paymentMethodArray,
            'data' => $data,
        ]);
    }
}
