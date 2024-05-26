<?php

use Dnw\Game\Livewire\CreateGameComponent;
use Illuminate\Support\Facades\Route;

Route::prefix('games/')->middleware('web')->name('game.')->group(function () {
    Route::get('create', CreateGameComponent::class)->middleware('auth:web')->name('create');
    Route::get('/{id}', CreateGameComponent::class)->name('show');
});
