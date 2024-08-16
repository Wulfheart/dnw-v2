<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dev-login', [\App\Http\Controllers\DevLoginController::class, 'show'])->name('dev-login');
