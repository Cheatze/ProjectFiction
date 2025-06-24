<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Story;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function showProfile(User $user)
    {
        //check if Auth::id() is subscribed to this user

        //$user = User::where('id', $id)->first();

        $id = $user->id;

        $currentUser = Auth::user();

        $list = Story::where('user_id', $id)
            ->select('id', 'title', 'genre', 'synopsis')
            ->orderBy('id', 'desc')
            ->paginate(15);

        $isSubscribed = false;

        if ($currentUser && $currentUser->id !== $id) {
            // Check if the current user is subscribed to the profile owner
            $isSubscribed = $currentUser->subscribedTo() //Ignore the red, this works
                ->where('subscribed_to_id', $id)
                ->exists();
        }

        return view('profile')
            ->with('user', $user)
            ->with('stories', $list)
            ->with('isSubscribed', $isSubscribed);
    }
}
