<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('index');
// })->name("index");

Route::get(uri: '/', action: [\App\Http\Controllers\MainController::class, 'index'])->name('index');

// Route::get('/register', function () {
//     return view('register');
// })->name("show.register");

Route::middleware('guest')->group(function () {
    Route::get(uri: '/register', action: [\App\Http\Controllers\AuthController::class, 'showRegister'])->name('show.register');
    Route::get(uri: '/login', action: [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('show.login');

    Route::post(uri: '/register', action: [\App\Http\Controllers\AuthController::class, 'register'])->name('register');
    Route::post(uri: '/login', action: [\App\Http\Controllers\AuthController::class, 'login'])->name('login');
});

Route::middleware('auth')->group(function () {
    Route::post(uri: '/logout', action: [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

    Route::get(uri: '/upload', action: [\App\Http\Controllers\StoriesController::class, 'showUpload'])->name('show.upload');
});



Route::get('/user/{id}', function (string $id) {
    return 'User ' . $id;
});