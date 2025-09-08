<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Requests\SendResetEmailRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{


    // public function showRegister()
    // {
    //     return view('auth.register');
    // }

    public function showLogin()
    {
        return view('auth.login');
    }

    // public function showReset()
    // {
    //     return view('auth.forgot-password');
    // }

    // /**
    //  * Validates email and sends password reset email
    //  * @param \Illuminate\Http\Request $request
    //  * @return \Illuminate\Http\RedirectResponse
    //  */
    // public function sendResetEmail(SendResetEmailRequest $request)
    // {
    //     //$request->validate(['email' => 'required|email']);

    //     $status = Password::sendResetLink(
    //         $request->only('email')
    //     );

    //     return $status === Password::ResetLinkSent
    //         ? back()->with(['status' => __($status)])
    //         : back()->withErrors(['email' => __($status)]);
    // }

    // /**
    //  * Shows the view with the form where the user can reset their password
    //  * Gives the token send to the user in the email to that view
    //  * @param mixed $token
    //  * @return \Illuminate\Contracts\View\View
    //  */
    // public function showResetForm($token)
    // {
    //     return view('auth.reset-password', ['token' => $token]);
    // }

    // /**
    //  * 
    //  * @param \Illuminate\Http\Request $request
    //  * @return \Illuminate\Http\RedirectResponse
    //  */
    // public function resetPassword(ResetPasswordRequest $request)
    // {

    //     $status = Password::reset(
    //         $request->only('email', 'password', 'password_confirmation', 'token'),
    //         function (User $user, string $password) {
    //             $user->forceFill([
    //                 'password' => Hash::make($password)
    //             ])->setRememberToken(Str::random(60));

    //             $user->save();

    //             event(new PasswordReset($user));
    //         }
    //     );

    //     return $status === Password::PasswordReset
    //         ? redirect()->route('show.login')->with('status', __($status))
    //         : back()->withErrors(['email' => [__($status)]]);
    // }

    // /**
    //  * Creates a new user in the db and automatically logs the user in with that account
    //  * @param \Illuminate\Http\Request $request
    //  * @return mixed|\Illuminate\Http\RedirectResponse
    //  */
    // public function register(RegisterUserRequest $request)
    // {

    //     $validated = $request->validated();

    //     $user = User::create($validated);
    //     event(new Registered($user));//This should trigger a listener that sends the email
    //     Auth::login($user);

    //     return redirect()->route('verification.notice');
    // }

    public function login(LoginRequest $request)
    {

        $validated = $request->validated();

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
