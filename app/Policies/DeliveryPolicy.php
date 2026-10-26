<?php

namespace App\Policies;

use App\Models\Delivery;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DeliveryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'Administrator';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Delivery $delivery): bool
    {
        if ($user->role === 'Administrator') {
            return true;
        }

        $order = $delivery->order;

        if ($user->role === 'Buyer' && $user->user_id === $order->buyer_id) {
            return true;
        }

        if ($user->role === 'Seller' && $user->user_id === $order->seller_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Delivery $delivery): bool
    {
        if ($user->role === 'Administrator') {
            return true;
        }

        $order = $delivery->order;

        if ($user->role === 'Seller' && $user->user_id === $order->seller_id) {
            return $order->order_status === 'Paid';
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Delivery $delivery): bool
    {
        return $user->role === 'Administrator';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Delivery $delivery): bool
    {
        return $user->role === 'Administrator';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Delivery $delivery): bool
    {
        return false;
    }
}
