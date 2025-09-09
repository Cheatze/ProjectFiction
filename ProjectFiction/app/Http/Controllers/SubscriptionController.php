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

    }
}
