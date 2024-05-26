<?php

use App\Livewire\DevLogin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dev-login', DevLogin::class)->name('dev-login');
