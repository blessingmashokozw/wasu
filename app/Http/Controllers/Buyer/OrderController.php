<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Listing;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\AuditLogger;
use App\Services\PaymentGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    protected PaymentGatewayService $paymentService;

    public function __construct(PaymentGatewayService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of the buyer's orders.
     */
    public function index(Request $request): Response
    {
        $query = Order::with(['seller.seller', 'orderItems.listing', 'delivery'])
            ->where('buyer_id', auth()->id());

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhereHas('seller.seller', function ($q) use ($search) {
                      $q->where('business_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('seller.user', function ($q) use ($search) {
                      $q->where('full_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('order_status', $request->input('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('order_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('order_date', '<=', $request->input('date_to'));
        }

        $orders = $query->orderBy('order_date', 'desc')->get();

        return Inertia::render('Buyer/Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'status', 'date_from', 'date_to']),
        ]);
    }

    /**
     * Show the checkout page.
     */
    public function checkout(Request $request): Response|RedirectResponse
    {
        
        Gate::authorize('create', Order::class);

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.listing_id' => 'required|exists:listings,listing_id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $items = collect($validated['items']);

        // Load listings with lock for update
        $listings = Listing::whereIn('listing_id', $items->pluck('listing_id'))
            ->where('listing_status', 'Active')
            ->where('quantity_available', '>', 0)
            ->lockForUpdate()
            ->get();

        // Check if all items exist and are available
        if ($listings->count() !== $items->count()) {
            return back()->with('error', 'Some items are no longer available.');
        }

        // Check if all items are from the same seller
        $sellers = $listings->pluck('seller_id')->unique();
        if ($sellers->count() > 1) {
            return back()->with('error', 'You can only order items from one seller at a time.');
        }

        // Check if quantities are available
        foreach ($items as $item) {
            $listing = $listings->firstWhere('listing_id', $item['listing_id']);
            if ($listing->quantity_available < $item['quantity']) {
                return back()->with('error', "Insufficient stock for {$listing->title}.");
            }
        }

        // Calculate total
        $total = 0;
        $orderItems = [];
        foreach ($items as $item) {
            $listing = $listings->firstWhere('listing_id', $item['listing_id']);
            $subtotal = $listing->price * $item['quantity'];
            $total += $subtotal;

            $orderItems[] = [
                'listing_id' => $listing->listing_id,
                'quantity' => $item['quantity'],
                'unit_price' => $listing->price,
                'subtotal' => $subtotal,
                'listing' => $listing,
            ];
        }

        $buyer = auth()->user()->buyer;

        return Inertia::render('Buyer/Orders/Checkout', [
            'items' => $orderItems,
            'total' => $total,
            'seller' => $listings->first()->seller,
            'delivery_address' => $buyer->delivery_address,
        ]);
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', Order::class);

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.listing_id' => 'required|exists:listings,listing_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|gt:0',
            'items.*.subtotal' => 'required|numeric|gt:0',
            'delivery_address' => 'required|string|max:500',
        ]);

        $order = DB::transaction(function () use ($validated, $request) {
            $items = collect($validated['items']);

            // Load listings with lock for update
            $listings = Listing::whereIn('listing_id', $items->pluck('listing_id'))
                ->where('listing_status', 'Active')
                ->where('quantity_available', '>', 0)
                ->lockForUpdate()
                ->get();

            // Verify all items are still available
            if ($listings->count() !== $items->count()) {
                throw new \Exception('Some items are no longer available.');
            }

            // Verify quantities
            foreach ($items as $item) {
                $listing = $listings->firstWhere('listing_id', $item['listing_id']);
                if ($listing->quantity_available < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$listing->title}.");
                }
            }

            // Calculate total
            $total = $items->sum('subtotal');

            // Create order
            $order = Order::create([
                'buyer_id' => auth()->id(),
                'seller_id' => $listings->first()->seller_id,
                'order_date' => now(),
                'total_amount' => $total,
                'delivery_address' => $validated['delivery_address'],
                'order_status' => 'Pending payment',
            ]);

            // Create order items (stock will be deducted after successful payment)
            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'listing_id' => $item['listing_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            return $order;
        });

        // Initiate payment
        $paymentData = [
            'order_id' => $order->order_id,
            'amount' => $order->total_amount,
            'buyer_id' => auth()->id(),
        ];

        $paymentResponse = $this->paymentService->initiate($paymentData);

        // Create payment record
        \App\Models\Payment::create([
            'order_id' => $order->order_id,
            'payment_method' => 'Paynow',
            'amount' => $order->total_amount,
            'transaction_ref' => $paymentResponse['reference'],
            'payment_status' => 'Initiated',
        ]);

        AuditLogger::logPayment(auth()->id(), 'initiated');

        // Redirect to intermediate page to avoid CORS issues
        return Inertia::render('Buyer/Orders/PaymentRedirect', [
            'redirect_url' => $paymentResponse['redirect_url'],
            'order_id' => $order->order_id,
            'amount' => $order->total_amount,
        ]);
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): Response
    {
        Gate::authorize('view', $order);

        $order->load(['seller', 'orderItems.listing', 'payments', 'delivery', 'reviews']);

        return Inertia::render('Buyer/Orders/Show', [
            'order' => $order,
        ]);
    }
}
