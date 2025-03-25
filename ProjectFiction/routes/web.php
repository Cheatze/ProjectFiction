<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::redirect('/here', '/greeting');

Route::get('/greeting', function () {
    return '<h1>Hello World<h1>';
});

Route::get('/user/{id}', function (string $id) {
    return 'User ' . $id;
});