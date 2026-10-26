<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Review $review): bool
    {
        return $user->user_id === $review->reviewer_id
            || $user->user_id === $review->reviewee_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, $order): bool
    {

  
        // Only the buyer can review the seller
        if ($user->role !== 'Buyer') {
            return false;
        }

        // Must be the buyer of the order
        if ($user->user_id !== $order->buyer_id) {
            return false;
        }

        // Order must be completed
        if ($order->order_status !== 'Delivered' && $order->order_status !== 'Completed  ') {
           // return false;
        }

        // Cannot review the same order twice
        if ($order->reviews()->where('reviewer_id', $user->user_id)->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Review $review): bool
    {
        // Only the reviewer can update their own review
        return $user->user_id === $review->reviewer_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Review $review): bool
    {
        // Only the reviewer can delete their own review
        return $user->user_id === $review->reviewer_id;
    }
}
