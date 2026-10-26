<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MessagePolicy
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
    public function view(User $user, Message $message): bool
    {
        if ($user->role === 'Administrator') {
            return true;
        }

        if ($user->user_id === $message->sender_id || $user->user_id === $message->receiver_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'Buyer' || $user->role === 'Seller';
    }

    /**
     * Determine whether the user can send a message about an order.
     */
    public function sendMessageForOrder(User $user, Message $message): bool
    {
        if (!$message->order_id) {
            return true;
        }

        $order = $message->order;

        if ($user->role === 'Buyer' && $user->user_id === $order->buyer_id) {
            return true;
        }

        if ($user->role === 'Seller' && $user->user_id === $order->seller_id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Message $message): bool
    {
        return $user->user_id === $message->sender_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Message $message): bool
    {
        return $user->user_id === $message->sender_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Message $message): bool
    {
        return $user->user_id === $message->sender_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Message $message): bool
    {
        return false;
    }
}
