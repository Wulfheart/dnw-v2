<?php

namespace Dnw\User\Infrastructure;

use Dnw\User\Application\Query\GetUsersByIds\GetUsersByIdsQueryHandlerInterface;
use Dnw\User\Infrastructure\Query\GetUsersByIdsLaravelQueryHandler;
use Illuminate\Support\ServiceProvider;

final class UserServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    public $bindings = [
        GetUsersByIdsQueryHandlerInterface::class => GetUsersByIdsLaravelQueryHandler::class,
    ];
}
