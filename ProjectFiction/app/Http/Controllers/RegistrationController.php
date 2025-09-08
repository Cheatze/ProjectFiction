<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Creates a new user in the db and automatically logs the user in with that account
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\RedirectResponse
     */
    public function register(RegisterUserRequest $request)
    {

        $validated = $request->validated();

        $user = User::create($validated);
        event(new Registered($user));//This should trigger a listener that sends the email
        Auth::login($user);

        return redirect()->route('verification.notice');
    }

}
