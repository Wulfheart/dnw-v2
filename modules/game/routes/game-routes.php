<?php

use Dnw\Game\Http\Controllers\CreateGame\CreateGameController;
use Illuminate\Support\Facades\Route;

Route::prefix('games/')->middleware('web')->name('game.')->group(function () {
    Route::get('create', [CreateGameController::class, 'get'])->name('create');
    Route::post('create', [CreateGameController::class, 'post'])->name('store');
});
