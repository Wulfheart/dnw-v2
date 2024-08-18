<?php

use App\Http\DevLogin\DevLoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dev-login', [DevLoginController::class, 'show'])->name('dev-login');
