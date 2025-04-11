<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{


    public function showRegister()
    {
        return view('auth.register');
    }

    public function showLogin()
    {
        return view('auth.login');
    }


    /**
     * Creates a new user in the db and automatically logs the user in with that account
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:users|max:120|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|max:120|min:8|confirmed'
        ]);

        $user = User::create($validated);

        Auth::login($user);

        return redirect()->route('index');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if (Auth::attempt($validated)) {
            $request->session()->regenerate();
            return redirect()->route('index');
        }
        throw ValidationException::withMessages([
            'Credentials' => 'Wrong email or password'
        ]);
    }

    /**
     * Logs out the user removes the session regenerates the token and redirects to the login page
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('show.login');
    }

}
