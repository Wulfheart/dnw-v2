<?php

use Dnw\Game\Http\Controllers\CreateGame\CreateGameController;
use Dnw\Game\Livewire\CreateGameComponent;
use Illuminate\Support\Facades\Route;

Route::prefix('games/')->middleware('web')->name('game.')->group(function () {
    Route::get('mojo', [CreateGameController::class, 'get']);

    Route::get('create', CreateGameComponent::class)->name('create');
    Route::post('create', [CreateGameController::class, 'post'])->name('store');
});
