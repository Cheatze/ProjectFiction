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
        $user = User::where('id', $id)->first();

        $list = Story::where('user_id', $id)
            ->select('id', 'title', 'genre', 'synopsis')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('profile')->with('user', $user)->with('stories', $list);
    }

    public function subscribeToUser(Request $request)
    {

    }
}
