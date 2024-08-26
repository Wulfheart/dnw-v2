<?php

namespace Dnw\Game\Providers;

use Dnw\Game\Core\Application\Query\GetAllVariants\GetAllVariantsQueryHandlerInterface;
use Dnw\Game\Core\Domain\Adapter\RandomNumberGenerator\RandomNumberGeneratorInterface;
use Dnw\Game\Core\Domain\Adapter\TimeProvider\TimeProviderInterface;
use Dnw\Game\Core\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Player\Repository\Player\PlayerRepositoryInterface;
use Dnw\Game\Core\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Core\Infrastructure\Adapter\LaravelTimeProvider;
use Dnw\Game\Core\Infrastructure\Adapter\RandomNumberGenerator;
use Dnw\Game\Core\Infrastructure\Query\GetAllVariants\GetAllVariantsLaravelQueryHandler;
use Dnw\Game\Core\Infrastructure\Repository\Game\LaravelGameRepository;
use Dnw\Game\Core\Infrastructure\Repository\Player\LaravelPlayerRepository;
use Dnw\Game\Core\Infrastructure\Repository\Variant\LaravelVariantRepository;
use Illuminate\Support\ServiceProvider;

class GameServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    public array $bindings = [
        VariantRepositoryInterface::class => LaravelVariantRepository::class,
        GameRepositoryInterface::class => LaravelGameRepository::class,
        PlayerRepositoryInterface::class => LaravelPlayerRepository::class,
        TimeProviderInterface::class => LaravelTimeProvider::class,
        RandomNumberGeneratorInterface::class => RandomNumberGenerator::class,
        GetAllVariantsQueryHandlerInterface::class => GetAllVariantsLaravelQueryHandler::class,
    ];
}
