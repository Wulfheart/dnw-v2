<?php

namespace Dnw\Foundation\Providers;

use Dnw\Foundation\Event\DomainEventProvider;
use Dnw\Foundation\Event\EventDispatcherInterface;
use Dnw\Foundation\Event\LaravelEventDispatcher;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Filesystem\Filesystem;

class FoundationEventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            EventDispatcherInterface::class,
            function (Application $app) {
                return new LaravelEventDispatcher(
                    $app->make(Queue::class),
                    $app->make(DomainEventProvider::class)->getEvents(),
                    $app
                );
            }
        );

        $this->app->bind(
            DomainEventProvider::class,
            function(Application $app) {
                return new DomainEventProvider(
                    $app->bootstrapPath('cache/dnw_events.php'),
                    $app->make(Filesystem::class)
                );
            }
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
