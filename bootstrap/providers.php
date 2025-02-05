<?php

use App\Providers\AppServiceProvider;
use App\Providers\ModuleProvider;
use Dnw\Foundation\Providers\FoundationBusServiceProvider;
use Dnw\Foundation\Providers\FoundationEventServiceProvider;
use Dnw\Foundation\Providers\FoundationServiceProvider;
use Dnw\Game\Infrastructure\GameServiceProvider;
use Dnw\User\Infrastructure\UserServiceProvider;

return [
    AppServiceProvider::class,
    ModuleProvider::class,
    FoundationBusServiceProvider::class,
    FoundationEventServiceProvider::class,
    FoundationServiceProvider::class,
    GameServiceProvider::class,
    UserServiceProvider::class,
];
