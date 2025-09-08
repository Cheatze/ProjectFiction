<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Requests\SubscribeRequest;
use App\Http\Requests\UnsubscribeRequest;

class SubscriptionService
{
    /**
     * Subscribe one user to another.
     *
     * @param User $subscriber
     * @param int $subscribedToId
     * @return bool
     */
    public function subscribe(User $subscriber, int $subscribedToId): bool
    {
        try {
            // The attach method automatically prevents duplicate entries.
            $subscriber->subscribedTo()->attach($subscribedToId);
            return true;
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            return false;
        }
    }

    /**
     * Unsubscribe one user from another.
     *
     * @param User $subscriber
     * @param int $subscribedToId
     * @return bool
     */
    public function unsubscribe(User $subscriber, int $subscribedToId): bool
    {
        try {
            // The detach method removes the entry from the pivot table.
            $detachedCount = $subscriber->subscribedTo()->detach($subscribedToId);
            return $detachedCount > 0;
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            return false;
        }
    }


}
