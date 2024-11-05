<?php

use App\Providers\AppServiceProvider;
use App\Providers\ModuleProvider;
use Dnw\Foundation\Providers\FoundationEventServiceProvider;

return [
    AppServiceProvider::class,
    ModuleProvider::class,
    FoundationEventServiceProvider::class,
];
