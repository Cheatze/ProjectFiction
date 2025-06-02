<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Route::get('/', function () {
//     return view('index');
// })->name("index");

/**
 * Shows the main/index page
 */
Route::get(uri: '/', action: [\App\Http\Controllers\MainController::class, 'index'])->name('index');

/**
 * Shows the pagination of stories from new to old
 */
Route::get(uri: '/browse', action: [\App\Http\Controllers\StoriesController::class, 'showNew'])->name('show.newest');

/**
 * Shows the pagination of stories from new to old by genre
 */
Route::get('/stories/{genre}', [\App\Http\Controllers\StoriesController::class, 'showGenre'])->name('stories.genre');


/**
 * Shows the read page for the story with a certain id
 */
Route::get('/story/{id}', [\App\Http\Controllers\StoriesController::class, 'showStory'])->name('stories.read');

//Make a UserController?
Route::get('/profile/{id}', [\App\Http\Controllers\UserController::class, 'showProfile'])->name('profile.show');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/')->with('message', 'Email verified!');
})->middleware(['auth', 'signed'])->name('verification.verify');

// Route::get('/register', function () {
//     return view('register');
// })->name("show.register");

Route::middleware('guest')->group(function () {
    Route::get(uri: '/register', action: [\App\Http\Controllers\AuthController::class, 'showRegister'])->name('show.register');
    Route::get(uri: '/login', action: [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('show.login');

    Route::post(uri: '/register', action: [\App\Http\Controllers\AuthController::class, 'register'])->name('register');
    Route::post(uri: '/login', action: [\App\Http\Controllers\AuthController::class, 'login'])->name('login');
});

//Routes only for those who are logged in
Route::middleware('auth')->group(function () {
    Route::post(uri: '/logout', action: [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

});

//Routes only for those who are logged in and verified
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get(uri: '/write', action: [\App\Http\Controllers\StoriesController::class, 'showWrite'])->name('show.write');
    //The route for submitting a story
    Route::post(uri: '/write', action: [\App\Http\Controllers\StoriesController::class, 'submitStory'])->name('write');
    //The rote for deleting a story
    Route::post(uri: '/delete', action: [\App\Http\Controllers\StoriesController::class, 'deleteStory'])->name('delete');
    //The route for subscribing to a user
    Route::post(uri: '/subscribe', action: [\App\Http\Controllers\UserController::class, 'subscribeToUser'])->name('subscribe');
});

//Route to verification reminder and verification email resend form
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

//Resends the verification email
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


Route::get('/user/{id}', function (string $id) {
    return 'User ' . $id;
});