<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('index');
// })->name("index");

Route::get(uri: '/', action: [\App\Http\Controllers\MainController::class, 'index'])->name('index');

// Route::get('/register', function () {
//     return view('register');
// })->name("show.register");


Route::get(uri: '/register', action: [\App\Http\Controllers\AuthController::class, 'showRegister'])->name('show.register');
Route::get(uri: '/login', action: [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('show.login');


Route::get('/user/{id}', function (string $id) {
    return 'User ' . $id;
});