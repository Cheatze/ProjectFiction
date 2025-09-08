<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Requests\SubscribeRequest;
use App\Http\Requests\UnsubscribeRequest;
use App\Services\SubscriptionService;

class SubscriptionController extends Controller
{
    public function subscribeToUser(SubscribeRequest $request, SubscriptionService $subscriptionService)
    {
        $subscriberId = Auth::id();
        $subscriber = User::find($subscriberId);
        $subscribedToId = $request->input('id');

        if ($subscriptionService->subscribe($subscriber, $subscribedToId)) {
            return back();
        } else {
            return back()->with('error', 'Could not subscribe. Perhaps you are already subscribed.');
        }

        // try {
        //     // Attach the subscription
        //     // The `attach` method automatically handles inserting into the pivot table.
        //     // It also automatically prevents duplicate entries if you have the unique constraint in your migration.
        //     $subscriber->subscribedTo()->attach($subscribedToId);

        //     return back();

        // } catch (\Exception $e) {
        //     return back()->with('error', 'Could not subscribe. Perhaps you are already subscribed.');
        // }

    }

    public function unsubscribeFromUser(UnsubscribeRequest $request, SubscriptionService $subscriptionService)
    {

        $subscriberId = Auth::id(); // The ID of the current authenticated user
        $subscribedToId = $request->input('id');
        $subscriber = Auth::user();

        if ($subscriptionService->unsubscribe($subscriber, $subscribedToId)) {
            return back();
        } else {
            return back()->with('error', 'Could not unsubscribe. Perhaps you were not subscribed.');
        }

        // try {
        //     // Detach the subscription
        //     // The `detach` method removes the entry from the pivot table.
        //     // It will silently do nothing if the subscription doesn't exist.
        //     $detachedCount = $subscriber->subscribedTo()->detach($subscribedToId);

        //     if($subscriptionService->unsubscribe($subscriber, $subscribedToId)) {
        //         return back();
        //     } else {
        //         return back()->with('error', 'Could not unsubscribe. Perhaps you were not subscribed.');
        //     }

        //     if ($detachedCount > 0) {
        //         return back();
        //     } else {
        //         // This case handles if they tried to unsubscribe from someone they weren't subscribed to
        //         return back()->with('error', 'You were not subscribed to this user.');
        //     }

        // } catch (\Exception $e) {
        //     // Handle any database errors
        //     return back()->with('error', 'An error occurred while unsubscribing.');
        // }

    }
}
