<?php

// use Dnw\Game\Http\Controllers\GameController;

// Route::get('/games', [GameController::class, 'index'])->name('games.index');
// Route::get('/games/create', [GameController::class, 'create'])->name('games.create');
// Route::post('/games', [GameController::class, 'store'])->name('games.store');
// Route::get('/games/{game}', [GameController::class, 'show'])->name('games.show');
// Route::get('/games/{game}/edit', [GameController::class, 'edit'])->name('games.edit');
// Route::put('/games/{game}', [GameController::class, 'update'])->name('games.update');
// Route::delete('/games/{game}', [GameController::class, 'destroy'])->name('games.destroy');

use Dnw\Game\Http\Controllers\CreateGame\CreateGameController;
use Illuminate\Support\Facades\Route;

Route::prefix('games/')->middleware('web')->name('game.')->group(function () {
    Route::get('create', [CreateGameController::class, 'get'])->name('create');
    Route::post('create', [CreateGameController::class, 'post'])->name('store');
});
