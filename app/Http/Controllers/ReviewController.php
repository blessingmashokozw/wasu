<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    /**
     * Store a newly created review.
     */
    public function store(ReviewRequest $request, Order $order): RedirectResponse
    {
        Gate::authorize('create', [Review::class, $order]);

        $revieweeId = $request->review_type === 'buyer' ? $order->buyer_id : $order->seller_id;

        $review = Review::create([
            'reviewer_id' => auth()->id(),
            'reviewee_id' => $revieweeId,
            'order_id' => $order->order_id,
            'review_type' => $request->review_type,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Review submitted successfully.');
    }

    /**
     * Update the specified review.
     */
    public function update(ReviewRequest $request, Review $review): RedirectResponse
    {
        Gate::authorize('update', $review);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Review updated successfully.');
    }

    /**
     * Remove the specified review.
     */
    public function destroy(Review $review): RedirectResponse
    {
        Gate::authorize('delete', $review);

        $review->delete();

        return redirect()->back()->with('success', 'Review deleted successfully.');
    }

    /**
     * Show the review form for a specific order.
     */
    public function create(Order $order): Response
    {
        // Gate::authorize('create', [Review::class, $order]);

        $order->load(['seller.seller', 'orderItems.listing']);

        return Inertia::render('Reviews/Create', [
            'order' => $order,
        ]);
    }

    /**
     * Show the edit form for a specific review.
     */
    public function edit(Review $review): Response
    {
        Gate::authorize('update', $review);

        $review->load(['order', 'reviewee', 'reviewer']);

        return Inertia::render('Reviews/Edit', [
            'review' => $review,
        ]);
    }
}
