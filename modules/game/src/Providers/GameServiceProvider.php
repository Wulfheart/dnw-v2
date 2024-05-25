<?php

namespace Dnw\Game\Providers;

use Dnw\Game\Core\Application\Query\GetAllVariants\GetAllVariantsQueryHandlerInterface;
use Dnw\Game\Core\Domain\Adapter\RandomNumberGenerator\RandomNumberGeneratorInterface;
use Dnw\Game\Core\Domain\Adapter\TimeProvider\TimeProviderInterface;
use Dnw\Game\Core\Domain\Game\Repository\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Core\Infrastructure\Adapter\LaravelTimeProvider;
use Dnw\Game\Core\Infrastructure\Adapter\RandomNumberGenerator;
use Dnw\Game\Core\Infrastructure\Query\GetAllVariants\GetAllVariantsLaravelQueryHandler;
use Dnw\Game\Core\Infrastructure\Repository\Game\LaravelGameRepository;
use Dnw\Game\Core\Infrastructure\Repository\Variant\LaravelVariantRepository;
use Illuminate\Support\ServiceProvider;

class GameServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    public array $bindings = [
        VariantRepositoryInterface::class => LaravelVariantRepository::class,
        GameRepositoryInterface::class => LaravelGameRepository::class,
        TimeProviderInterface::class => LaravelTimeProvider::class,
        RandomNumberGeneratorInterface::class => RandomNumberGenerator::class,
        GetAllVariantsQueryHandlerInterface::class => GetAllVariantsLaravelQueryHandler::class,
    ];

    public function register()
    {
    }

    public function boot(): void
    {
    }
}
