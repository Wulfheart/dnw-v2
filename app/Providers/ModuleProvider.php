<?php

namespace App\Providers;

use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Finder\Finder;

class ModuleProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->resolving(Migrator::class, function (Migrator $migrator) {
            $migrations = Finder::create()->in($this->app->basePath('modules'))->path('migrations')->directories();

            foreach ($migrations as $migration) {
                $migrator->path($migration->getPathname());
            }
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
