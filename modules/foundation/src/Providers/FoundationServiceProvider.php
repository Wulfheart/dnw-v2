<?php

namespace Dnw\Foundation\Providers;

use Dnw\Foundation\Event\EventDispatcherInterface;
use Dnw\Foundation\Event\LaravelEventDispatcher;
use Illuminate\Support\ServiceProvider;

class FoundationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            EventDispatcherInterface::class,
            LaravelEventDispatcher::class
        );
    }

    public function boot()
    {
    }
}
