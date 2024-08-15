<?php

namespace App\Providers;

use App\Navigation\NavigationItemNameEnum;
use App\Navigation\NavigationItemsViewModel;
use App\Navigation\NavigationItemViewModel;
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
