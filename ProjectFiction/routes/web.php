<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Enums\Genre;
use App\Http\Middleware\EnsureUserOwnsProfile;

//Controllers for grouping
use App\Http\Controllers\StoriesController;
// use App\Http\Controllers\UserController;
// use App\Http\Controllers\AuthController;

//use App\Services\SubscriptionService;

// Route::get('/', function () {
//     return view('index');
// })->name("index");

// Routes that use the StoriesController
Route::controller(StoriesController::class)->group(function () {
    //Paginated stories from new to old
    Route::get('/browse', 'showNew')->name('show.newest');
    //Paginated stories by score
    Route::get('/popular', 'showPopular')->name('show.popular');
    // Paginated stories from new to old by genre
    Route::get('/stories/{genre}', 'showGenre')->name('stories.genre');
    //Route for the search bar
    Route::get('/search', 'showSearch')->name('show.search');
    //Shows the read page for the story with a certain id
    Route::get('/story/{story}', 'showStory')->name('stories.read');
    //Route for a random story
    Route::get('/random', 'random')->name('stories.random');
});


/**
 * Shows the main/index page
 */
Route::get(uri: '/', action: [\App\Http\Controllers\MainController::class, 'index'])->name('index');


/**
 * Route to public profile
 */
Route::get('/profile/{user}', [\App\Http\Controllers\ProfileController::class, 'showProfile'])->name('profile.show');

/**
 * Route to the private profile 
 * With custom middleware to make sure the user can only reach their own
 */
Route::middleware(['auth', EnsureUserOwnsProfile::class])->group(function () {
    Route::get('/privateprofile/{user}', [\App\Http\Controllers\ProfileController::class, 'showPrivateProfile'])->name('privateprofile.show');
});

/**
 * Route for the link in a verification email
 */
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/')->with('message', 'Email verified!');
})->middleware(['auth', 'signed'])->name('verification.verify');


/**
 * Routes for those not logged in
 */
Route::middleware('guest')->group(function () {
    Route::get(uri: '/register', action: [\App\Http\Controllers\AuthController::class, 'showRegister'])->name('show.register');
    Route::get(uri: '/login', action: [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('show.login');

    Route::post(uri: '/register', action: [\App\Http\Controllers\AuthController::class, 'register'])->name('register');
    Route::post(uri: '/login', action: [\App\Http\Controllers\AuthController::class, 'login'])->name('login');

    Route::get(uri: '/forgot-password', action: [\App\Http\Controllers\AuthController::class, 'showReset'])->name('password.request');
    Route::post(uri: '/forgot-password', action: [\App\Http\Controllers\AuthController::class, 'sendResetEmail'])->name('password.email');
    Route::get(uri: '/forgot-password/{token}', action: [\App\Http\Controllers\AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post(uri: '/reset-password', action: [\App\Http\Controllers\AuthController::class, 'resetPassword'])->name('password.update');
});

//Routes only for those who are logged in
Route::middleware('auth')->group(function () {
    Route::post(uri: '/logout', action: [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

});

//Routes only for those who are logged in and verified
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get(uri: '/write', action: [\App\Http\Controllers\StoriesController::class, 'showWrite'])->name('show.write');
    //The route for submitting a story plus rate limiting middleware
    Route::post(uri: '/write', action: [\App\Http\Controllers\StoriesController::class, 'submitStory'])->middleware(['throttle:minute-submit-limiter', 'throttle:hour-submit-limiter', 'throttle:day-submit-limiter'])->name('write');
    //The rote for deleting a story
    Route::post(uri: '/delete/{story}', action: [\App\Http\Controllers\StoriesController::class, 'deleteStory'])->name('delete');
    //The route for subscribing to a user
    Route::post(uri: '/subscribe', action: [\App\Services\SubscriptionService::class, 'subscribeToUser'])->name('subscribe');
    //The route for unsubscribing from a user
    Route::delete(uri: '/unsubscribe', action: [\App\Services\SubscriptionService::class, 'unsubscribeFromUser'])->name('unsubscribe');
    //The route for liking a story
    Route::post('/stories/like/{story}', [\App\Http\Controllers\LikesController::class, 'like'])->name('stories.like');
    //The route for unliking a story
    Route::post('/stories/unlike/{story}', [\App\Http\Controllers\LikesController::class, 'unlike'])->name('stories.dislike');
});

//  

//Route to verification reminder and verification email resend form
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

//Resends the verification email
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

//
Route::get('/user/{id}', function (string $id) {
    return 'User ' . $id;
});