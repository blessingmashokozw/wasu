<?php

namespace App\Policies;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ListingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Listing $listing): bool
    {
        return $listing->listing_status === 'Active';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
       
        if ($user->role !== 'Seller') {
            return false;
        }

        return $user->seller && $user->seller->verification_status === 'Verified';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Listing $listing): bool
    {
        if ($user->role === 'Administrator') {
            return true;
        }

        return $user->user_id === $listing->seller_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Listing $listing): bool
    {
        if ($user->role === 'Administrator') {
            return true;
        }

        return $user->user_id === $listing->seller_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Listing $listing): bool
    {
        return $user->role === 'Administrator';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Listing $listing): bool
    {
        return false;
    }
}
