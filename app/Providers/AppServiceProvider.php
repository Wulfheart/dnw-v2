<?php

namespace App\Providers;

use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();

        FilamentColor::register([
            'danger' => Color::Red,
            'gray' => Color::Gray,
            'info' => Color::Blue,
            'primary' => Color::Emerald,
            'success' => Color::Green,
            'warning' => Color::Amber,
            'black' => Color::hex('#000000'),
        ]);
    }
}
