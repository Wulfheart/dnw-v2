<?php

use App\Http\DevLogin\DevLoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// TODO: Only in dev mode
Route::get('login', fn () => redirect(route('dev-login.show')))->name('login');
Route::delete('logout', function () {
    auth()->logout();

    return redirect(route('login'));
})->name('login');

Route::get('/dev-login', [DevLoginController::class, 'show'])->name('dev-login.show');
Route::post('/dev-login', [DevLoginController::class, 'login'])->name('dev-login.login');
