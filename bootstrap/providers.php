<?php

use App\Providers\AppServiceProvider;
use App\Providers\ModuleProvider;
use App\Providers\SoloServiceProvider;
use Dnw\Foundation\Providers\FoundationBusServiceProvider;
use Dnw\Foundation\Providers\FoundationEventServiceProvider;
use Dnw\Foundation\Providers\FoundationServiceProvider;
use Dnw\Game\Infrastructure\GameServiceProvider;

return [
    AppServiceProvider::class,
    ModuleProvider::class,
    SoloServiceProvider::class,
    FoundationBusServiceProvider::class,
    FoundationEventServiceProvider::class,
    FoundationServiceProvider::class,
    GameServiceProvider::class,
];
