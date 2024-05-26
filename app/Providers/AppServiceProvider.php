<?php

namespace App\Providers;

use App\Navigation\NavigationItemNameEnum;
use App\Navigation\NavigationItemsViewModel;
use App\Navigation\NavigationItemViewModel;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;
use Livewire\Features\SupportPageComponents\PageComponentConfig;

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

        // Navigation
        Facades\View::composer('*', function (View $view) {
            $view->with('navigation', new NavigationItemsViewModel([
                new NavigationItemViewModel(NavigationItemNameEnum::GAMES, 'Spiele', 'games.index'),
            ]));
        });

        View::macro('active', function (NavigationItemNameEnum $active) {
            // @phpstan-ignore-next-line
            if (! isset($this->layoutConfig)) {
                $this->layoutConfig = new PageComponentConfig();
            }

            $this->layoutConfig->mergeParams(['active' => $active]);

            return $this;
        });
    }
}
