<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function isSubscribed(User $currentUser, User $profileOwner): bool
    {
        if ($currentUser->id === $profileOwner->id) {
            return false;
        }

        // Check if the current user is subscribed to the profile owner
        return $currentUser->subscribedTo()
            ->where('subscribed_to_id', $profileOwner->id)
            ->exists();

    }
}
