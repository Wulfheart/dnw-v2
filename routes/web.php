<?php

use App\Http\DevLogin\DevLoginController;
use App\Web\Game\CreateGame\CreateGameController;
use App\Web\Game\GamePanel\GamePanelController;
use App\Web\Game\ListNewGames\ListNewGamesController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Mauricius\LaravelHtmx\Http\HtmxResponseClientRedirect;

Route::get('/', function () {
    $dates = [
        now()->addDays(),
        now()->addDays(2),
        now()->addMinutes(5)->addSeconds(10),
        now()->addSeconds(30),
        now()->addSeconds(5),
    ];

    $dates = array_map(fn (Carbon $date) => $date->unix(), $dates);

    return view('welcome', [
        'dates' => $dates,
    ]);
});

Route::get('/ping', function () {
    return 'Pong';
});
// TODO: Only in dev mode
Route::get('login', fn () => redirect(route('dev-login.show')))->name('login');
Route::delete('logout', function () {
    Auth::logout();

    return new HtmxResponseClientRedirect(route('login'));
})->name('logout');

Route::get('/dev-login', [DevLoginController::class, 'show'])->name('dev-login.show');
Route::post('/dev-login', [DevLoginController::class, 'login'])->name('dev-login.login');

/**
 * GAME
 */
Route::prefix('games/')
    ->middleware(['web', 'auth:web'])
    ->group(function () {
        Route::name('game.')->group(function () {
            Route::get('create', [CreateGameController::class, 'show'])->name('create');
            Route::post('create', [CreateGameController::class, 'store'])->name('store');
            Route::get('{id}', [GamePanelController::class, 'show'])->name('show');
        });
        Route::name('games.list.')->group(function () {
            Route::get('new', [ListNewGamesController::class, 'show'])->name('new');
        });
    });
