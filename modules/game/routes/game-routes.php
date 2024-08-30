<?php

use Dnw\Game\Http\CreateGame\CreateGameController;
use Dnw\Game\Http\GamePanel\GamePanelController;
use Illuminate\Support\Facades\Route;

Route::prefix('games/')->middleware(['web', 'auth:web'])->name('game.')->group(function () {
    Route::get('create', [CreateGameController::class, 'show'])->name('create');
    Route::post('create', [CreateGameController::class, 'store'])->name('store');
    Route::get('{id}', [GamePanelController::class, 'show'])->name('show');
});
