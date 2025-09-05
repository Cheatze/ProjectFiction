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
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function subscribeToUser(SubscribeRequest $request)
    {
        $subscriberId = Auth::id();
        $subscriber = User::find($subscriberId);
        $subscribedToId = $request->input('id');

        //del
        // // Ensure the user is not trying to subscribe to themselves
        // if ($subscriberId == $subscribedToId) {
        //     return back()->with('error', 'You cannot subscribe to yourself.');
        // }

        // // Get the current authenticated user
        // $subscriber = User::find($subscriberId);

        // if (!$subscriber) {
        //     // This should ideally not happen if Auth::id() returns a valid ID
        //     return back()->with('error', 'Subscriber not found.');
        // }

        // // Check if the user being subscribed to exists
        // $subscribedToUser = User::find($subscribedToId);
        // if (!$subscribedToUser) {
        //     return back()->with('error', 'User to subscribe to not found.');
        // }
        //del

        try {
            // Attach the subscription
            // The `attach` method automatically handles inserting into the pivot table.
            // It also automatically prevents duplicate entries if you have the unique constraint in your migration.
            $subscriber->subscribedTo()->attach($subscribedToId);

            return back();

        } catch (\Exception $e) {
            return back()->with('error', 'Could not subscribe. Perhaps you are already subscribed.');
        }

    }

    public function unsubscribeFromUser(UnsubscribeRequest $request)
    {

        $subscriberId = Auth::id(); // The ID of the current authenticated user
        $subscribedToId = $request->input('id');

        $subscriber = Auth::user();

        try {
            // Detach the subscription
            // The `detach` method removes the entry from the pivot table.
            // It will silently do nothing if the subscription doesn't exist.
            $detachedCount = $subscriber->subscribedTo()->detach($subscribedToId);

            if ($detachedCount > 0) {
                return back();
            } else {
                // This case handles if they tried to unsubscribe from someone they weren't subscribed to
                return back()->with('error', 'You were not subscribed to this user.');
            }

        } catch (\Exception $e) {
            // Handle any database errors
            return back()->with('error', 'An error occurred while unsubscribing.');
        }

    }



}
