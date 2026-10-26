<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Order;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the buyer's deliveries.
     */
    public function index(Request $request): Response
    {
        $query = Delivery::with(['order.seller', 'order.orderItems.listing', 'updates.updatedBy'])
            ->whereHas('order', function ($query) {
                $query->where('buyer_id', auth()->id());
            });

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('order', function ($q) use ($search) {
                    $q->where('order_id', 'like', "%{$search}%")
                      ->orWhereHas('seller', function ($q) use ($search) {
                          $q->where('business_name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('seller.user', function ($q) use ($search) {
                          $q->where('full_name', 'like', "%{$search}%");
                      });
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('current_status', $request->input('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $deliveries = $query->orderBy('created_at', 'desc')->get();

        return Inertia::render('Buyer/Deliveries/Index', [
            'deliveries' => $deliveries,
            'filters' => $request->only(['search', 'status', 'date_from', 'date_to']),
        ]);
    }

    /**
     * Show delivery details with timeline.
     */
    public function show(Delivery $delivery): Response
    {
        Gate::authorize('view', $delivery);

        $delivery->load(['order.seller', 'order.orderItems.listing', 'updates.updatedBy']);

        return Inertia::render('Buyer/Deliveries/Show', [
            'delivery' => $delivery,
        ]);
    }

    /**
     * Confirm receipt of delivery.
     */
    public function confirmReceipt(Request $request, Delivery $delivery)
    {
        Gate::authorize('view', $delivery);

        $order = $delivery->order;

        // Only allow confirmation when Delivered
        if ($delivery->current_status !== 'Delivered') {
            return back()->with('error', 'Can only confirm receipt when delivery status is Delivered');
        }

        DB::transaction(function () use ($delivery, $order) {
            // Update delivery
            $delivery->update([
                'current_status' => 'Received',
                'received_at' => now(),
            ]);

            // Update order
            $order->update([
                'order_status' => 'Completed',
            ]);

            AuditLogger::logDeliveryUpdate(auth()->id(), 'Received');
        });

        return back()->with('success', 'Receipt confirmed successfully.');
    }
}
