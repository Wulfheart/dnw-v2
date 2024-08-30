<?php

namespace Dnw\Game\Providers;

use Dnw\Adjudicator\AdjudicatorService;
use Dnw\Adjudicator\Json\JsonHandler;
use Dnw\Adjudicator\Uri;
use Dnw\Adjudicator\WebAdjudicatorImplementation;
use Dnw\Game\Core\Application\Query\GetAllVariants\GetAllVariantsQueryHandlerInterface;
use Dnw\Game\Core\Domain\Adapter\RandomNumberGenerator\RandomNumberGeneratorInterface;
use Dnw\Game\Core\Domain\Adapter\TimeProvider\TimeProviderInterface;
use Dnw\Game\Core\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Game\Repository\Phase\PhaseRepositoryInterface;
use Dnw\Game\Core\Domain\Player\Repository\Player\PlayerRepositoryInterface;
use Dnw\Game\Core\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Core\Infrastructure\Adapter\LaravelTimeProvider;
use Dnw\Game\Core\Infrastructure\Adapter\RandomNumberGenerator;
use Dnw\Game\Core\Infrastructure\Query\GetAllVariants\GetAllVariantsLaravelQueryHandler;
use Dnw\Game\Core\Infrastructure\Repository\Game\LaravelGameRepository;
use Dnw\Game\Core\Infrastructure\Repository\Phase\LaravelPhaseRepository;
use Dnw\Game\Core\Infrastructure\Repository\Player\LaravelPlayerRepository;
use Dnw\Game\Core\Infrastructure\Repository\Variant\LaravelVariantRepository;
use Dnw\Game\Helper\PhaseLengthFormatter;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

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
