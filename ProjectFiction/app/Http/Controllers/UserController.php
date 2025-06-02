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

        $list = Story::where('user_id', $id)
            ->select('id', 'title', 'genre', 'synopsis')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('profile')->with('user', $user)->with('stories', $list);
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
            // You might want more specific error handling, e.g., checking for IntegrityConstraintViolationException
            // if the unique constraint is the primary concern.
            return back()->with('error', 'Could not subscribe. Perhaps you are already subscribed.');
        }

    }

    public function unsubscribeToUser(Request $request)
    {

    }
}
