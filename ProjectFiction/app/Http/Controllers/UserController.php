<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Story;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function showProfile($id)
    {
        //check if Auth::id() is subscribed to this user

        $user = User::where('id', $id)->first();

        $currentUser = Auth::user();

        $list = Story::where('user_id', $id)
            ->select('id', 'title', 'genre', 'synopsis')
            ->orderBy('id', 'desc')
            ->paginate(15);

        $isSubscribed = false;

        if ($currentUser && $currentUser->id !== $id) {
            // Check if the current user is subscribed to the profile owner
            $isSubscribed = $currentUser->subscribedTo()
                ->where('subscribed_to_id', $id)
                ->exists();
        }

        return view('profile')
            ->with('user', $user)
            ->with('stories', $list)
            ->with('isSubscribed', $isSubscribed);
    }

    public function subscribeToUser(Request $request)
    {
        $subscriberId = Auth::id();
        $subscribedToId = $request->input('id');

        // Ensure the user is not trying to subscribe to themselves
        if ($subscriberId == $subscribedToId) {
            return back()->with('error', 'You cannot subscribe to yourself.');
        }

        // Get the current authenticated user
        $subscriber = User::find($subscriberId);

        if (!$subscriber) {
            // This should ideally not happen if Auth::id() returns a valid ID
            return back()->with('error', 'Subscriber not found.');
        }

        // Check if the user being subscribed to exists
        $subscribedToUser = User::find($subscribedToId);
        if (!$subscribedToUser) {
            return back()->with('error', 'User to subscribe to not found.');
        }

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

    public function unsubscribeFromUser(Request $request)
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
                return back()->with('info', 'You were not subscribed to this user.');
            }

        } catch (\Exception $e) {
            // Handle any database errors
            return back()->with('error', 'An error occurred while unsubscribing.');
        }

    }
}
