<?php

namespace Dnw\Game\Infrastructure;

use Dnw\Adjudicator\AdjudicatorService;
use Dnw\Adjudicator\Json\JsonHandler;
use Dnw\Adjudicator\Uri;
use Dnw\Adjudicator\WebAdjudicatorImplementation;
use Dnw\Game\Application\Query\GetAllVariants\GetAllVariantsQueryHandlerInterface;
use Dnw\Game\Domain\Adapter\RandomNumberGenerator\RandomNumberGeneratorInterface;
use Dnw\Game\Domain\Adapter\TimeProvider\TimeProviderInterface;
use Dnw\Game\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Domain\Game\Repository\Phase\PhaseRepositoryInterface;
use Dnw\Game\Domain\Player\Repository\Player\PlayerRepositoryInterface;
use Dnw\Game\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Helper\PhaseLengthFormatter;
use Dnw\Game\Infrastructure\Adapter\LaravelTimeProvider;
use Dnw\Game\Infrastructure\Adapter\RandomNumberGenerator;
use Dnw\Game\Infrastructure\Query\GetAllVariants\GetAllVariantsLaravelQueryHandler;
use Dnw\Game\Infrastructure\Repository\Game\LaravelGameRepository;
use Dnw\Game\Infrastructure\Repository\Phase\LaravelPhaseRepository;
use Dnw\Game\Infrastructure\Repository\Player\LaravelPlayerRepository;
use Dnw\Game\Infrastructure\Repository\Variant\LaravelVariantRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * @codeCoverageIgnore
 */
class GameServiceProvider extends ServiceProvider
{
    /** @var array<class-string, class-string> */
    public array $bindings = [
        VariantRepositoryInterface::class => LaravelVariantRepository::class,
        GameRepositoryInterface::class => LaravelGameRepository::class,
        PlayerRepositoryInterface::class => LaravelPlayerRepository::class,
        PhaseRepositoryInterface::class => LaravelPhaseRepository::class,
        TimeProviderInterface::class => LaravelTimeProvider::class,
        RandomNumberGeneratorInterface::class => RandomNumberGenerator::class,
        GetAllVariantsQueryHandlerInterface::class => GetAllVariantsLaravelQueryHandler::class,
    ];

    public function register()
    {
        $this->app->bind(AdjudicatorService::class, function (Application $app) {
            $httpFactory = $app->make(HttpFactory::class);

            return new WebAdjudicatorImplementation(
                $app->make(Client::class),
                $httpFactory,
                $httpFactory,
                new Uri(config('dnw.adjudicator.base_url')),
                new JsonHandler(),
            );
        });

        $this->app->bind(PhaseLengthFormatter::class, function (Application $app) {
            return new PhaseLengthFormatter($app->getLocale());
        });

    }
}
