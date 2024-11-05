<?php

use App\Providers\AppServiceProvider;
use App\Providers\ModuleProvider;
use Dnw\Foundation\Providers\FoundationEventServiceProvider;
use Illuminate\Support\ServiceProvider;
use Spatie\StructureDiscoverer\Discover;

$discovered = Discover::in(base_path('modules'))
    ->classes()
    ->extending(ServiceProvider::class)
    ->get();

return [
    AppServiceProvider::class,
    ModuleProvider::class,
    FoundationEventServiceProvider::class,
    ...$discovered,
];
