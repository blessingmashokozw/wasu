<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryUpdate;
use App\Models\Order;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the seller's orders with deliveries.
     */
    public function index(): Response
    {
        $orders = Order::with(['buyer', 'delivery', 'orderItems.listing'])
            ->where('seller_id', auth()->id())
            ->where('order_status', 'Paid')
            ->orderBy('order_date', 'desc')
            ->get();

        return Inertia::render('Seller/Deliveries/Index', [
            'orders' => $orders,
        ]);
    }

    /**
     * Update delivery status.
     */
    public function update(Request $request, Delivery $delivery)
    {
        Gate::authorize('update', $delivery);

        $validated = $request->validate([
            'status' => 'required|in:Processed,Dispatched,In transit,Delivered',
            'note' => 'nullable|string|max:500',
            'delivery_method' => 'nullable|string|max:100',
            'estimated_date' => 'nullable|date',
        ]);

        $order = $delivery->order;

        // Validate status sequence
        $validTransitions = [
            'Processed' => ['Dispatched'],
            'Dispatched' => ['In transit'],
            'In transit' => ['Delivered'],
            'Delivered' => [],
        ];

        $currentStatus = $delivery->current_status;
        $newStatus = $validated['status'];

        if (!in_array($newStatus, $validTransitions[$currentStatus] ?? [])) {
            return back()->with('error', 'Invalid status change. Allowed sequence: Processed -> Dispatched -> In transit -> Delivered');
        }

        DB::transaction(function () use ($delivery, $validated, $order) {
            // Update delivery
            $delivery->update([
                'current_status' => $validated['status'],
                'delivery_method' => $validated['delivery_method'] ?? $delivery->delivery_method,
                'estimated_date' => $validated['estimated_date'] ?? $delivery->estimated_date,
            ]);

            // Create delivery update record
            DeliveryUpdate::create([
                'delivery_id' => $delivery->delivery_id,
                'updated_by' => auth()->id(),
                'status' => $validated['status'],
                'note' => $validated['note'],
                'updated_at' => now(),
            ]);

            AuditLogger::logDeliveryUpdate(auth()->id(), $validated['status']);
        });

        return back()->with('success', 'Delivery status updated successfully.');
    }

    /**
     * Show delivery details with timeline.
     */
    public function show(Delivery $delivery): Response
    {
        Gate::authorize('view', $delivery);

        $delivery->load(['order.buyer', 'order.orderItems.listing', 'updates.updatedBy']);

        return Inertia::render('Seller/Deliveries/Show', [
            'delivery' => $delivery,
        ]);
    }
}
