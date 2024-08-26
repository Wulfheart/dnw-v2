<?php

use App\Http\DevLogin\DevLoginController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mauricius\LaravelHtmx\Http\HtmxResponseClientRedirect;

Route::get('/', function () {
    return view('welcome');
});

// TODO: Only in dev mode
Route::get('login', fn () => redirect(route('dev-login.show')))->name('login');
Route::delete('logout', function () {
    Auth::logout();

    return new HtmxResponseClientRedirect(route('login'));
})->name('logout');

Route::get('/dev-login', [DevLoginController::class, 'show'])->name('dev-login.show');
Route::post('/dev-login', [DevLoginController::class, 'login'])->name('dev-login.login');
