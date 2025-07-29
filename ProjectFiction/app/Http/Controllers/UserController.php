<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Story;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Shows the private profile of a user with their stories.
     * @param \App\Models\User $user
     * @return \Illuminate\Contracts\View\View
     */
    public function showPrivateProfile(User $user)
    {
        log::channel('users')->info('User accessed private profile', ['user_id' => $user->id]);

        $list = $user->stories() // Access the relationship
            ->select('id', 'title', 'genre', 'synopsis')
            ->orderBy('id', 'desc')
            ->paginate(15);

        log::channel('users')->info('Showing private profile stories list', ['user_id' => $user->id, 'count' => $list->count()]);

        return view('privateProfile')
            ->with('user', $user)
            ->with('stories', $list);
    }

    /**
     * Shows the public profile of a user with their stories.
     * @param \App\Models\User $user
     * @return \Illuminate\Contracts\View\View
     */
    public function showProfile(User $user)
    {
        //check if Auth::id() is subscribed to this user

        //$user = User::where('id', $id)->first();

        $id = $user->id;

        $currentUser = Auth::user();

        $list = $user->stories() // Access the relationship
            ->select('id', 'title', 'genre', 'synopsis')
            ->orderBy('id', 'desc')
            ->paginate(15);

        //$isSubscribed = false;
        $isSubscribed = $currentUser->can('isSubscribed', $user);
        //
        // if ($currentUser && $currentUser->id !== $id) {
        //     // Check if the current user is subscribed to the profile owner
        //     $isSubscribed = $currentUser->subscribedTo() //Ignore the red, this works
        //         ->where('subscribed_to_id', $id)
        //         ->exists();
        // }

        return view('profile')
            ->with('user', $user)
            ->with('stories', $list)
            ->with('isSubscribed', $isSubscribed);
    }
}
