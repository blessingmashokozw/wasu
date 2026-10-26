<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Buyer;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\Seller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class BuyerReportController extends Controller
{
    /**
     * Display a listing of the buyer's deliveries.
     */
    public function index(Request $request)
    {
        //for buyers
        $data = Buyer::with('user', 'orders.orderItems.listing.category', 'orders.delivery', 'orders.payments', 'orders.seller')
            ->where('user_id', auth()->id())->get();

        // Calculate aggregated statistics
        $buyer = $data->first();
        if (!$buyer) {
            return Inertia::render('Buyer/Reports/Index', [
                'summary' => [
                    'total_orders' => 0,
                    'total_spent' => 0,
                    'successful_orders' => 0,
                    'cancelled_orders' => 0,
                    'pending_orders' => 0,
                ],
                'monthly_spending' => [],
                'category_spending' => [],
                'seller_spending' => [],
                'order_status_distribution' => [],
                'data' => $data,
            ]);
        }

        $orders = $buyer->orders;
        $totalSpent = 0;
        $monthlySpending = [];
        $categorySpending = [];
        $sellerSpending = [];
        $orderStatusDistribution = [
            'Completed' => 0,
            'Paid' => 0,
            'Pending payment' => 0,
            'Cancelled' => 0,
        ];

        foreach ($orders as $order) {
            $orderAmount = (float) $order->total_amount;
            $totalSpent += $orderAmount;

            // Order status distribution
            if (isset($orderStatusDistribution[$order->order_status])) {
                $orderStatusDistribution[$order->order_status]++;
            }

            // Monthly spending
            $month = date('Y-m', strtotime($order->order_date));
            if (!isset($monthlySpending[$month])) {
                $monthlySpending[$month] = 0;
            }
            $monthlySpending[$month] += $orderAmount;

            // Category spending
            if ($order->order_items) {
                foreach ($order->order_items as $item) {
                    $category = $item->listing->category->name ?? 'Unknown';
                    if (!isset($categorySpending[$category])) {
                        $categorySpending[$category] = 0;
                    }
                    $categorySpending[$category] += (float) $item->subtotal;
                }
            }

            // Seller spending
            $sellerName = $order->seller->full_name ?? 'Unknown';
            if (!isset($sellerSpending[$sellerName])) {
                $sellerSpending[$sellerName] = 0;
            }
            $sellerSpending[$sellerName] += $orderAmount;
        }

        // Convert monthly spending to sorted array
        ksort($monthlySpending);
        $monthlySpendingArray = [];
        foreach ($monthlySpending as $month => $amount) {
            $monthlySpendingArray[] = [
                'month' => $month,
                'amount' => round($amount, 2),
            ];
        }

        // Convert category spending to array
        $categorySpendingArray = [];
        foreach ($categorySpending as $category => $amount) {
            $categorySpendingArray[] = [
                'category' => $category,
                'amount' => round($amount, 2),
            ];
        }

        // Convert seller spending to array
        $sellerSpendingArray = [];
        foreach ($sellerSpending as $seller => $amount) {
            $sellerSpendingArray[] = [
                'seller' => $seller,
                'amount' => round($amount, 2),
            ];
        }

        // Convert order status distribution to array
        $orderStatusArray = [];
        foreach ($orderStatusDistribution as $status => $count) {
            $orderStatusArray[] = [
                'status' => $status,
                'count' => $count,
            ];
        }

        return Inertia::render('Buyer/Reports/Index', [
            'summary' => [
                'total_orders' => $orders->count(),
                'total_spent' => round($totalSpent, 2),
                'successful_orders' => $orderStatusDistribution['Completed'] + $orderStatusDistribution['Paid'],
                'cancelled_orders' => $orderStatusDistribution['Cancelled'],
                'pending_orders' => $orderStatusDistribution['Pending payment'],
            ],
            'monthly_spending' => $monthlySpendingArray,
            'category_spending' => $categorySpendingArray,
            'seller_spending' => $sellerSpendingArray,
            'order_status_distribution' => $orderStatusArray,
            'data' => $data,
        ]);
    }
}