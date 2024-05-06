<?php

namespace Dnw\Foundation\Providers;

use Dnw\Foundation\Event\DomainEventProvider;
use Dnw\Foundation\Event\EventDispatcherInterface;
use Dnw\Foundation\Event\LaravelEventDispatcher;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class FoundationServiceProvider extends ServiceProvider
{
    public function register()
    {

    }

    public function boot()
    {
    }
}
