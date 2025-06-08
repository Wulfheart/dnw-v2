<?php

namespace Dnw\Game;

use App\Web\Game\Helper\PhaseLengthFormatter;
use Dnw\Adjudicator\AdjudicatorService;
use Dnw\Adjudicator\Json\JsonHandler;
use Dnw\Adjudicator\Uri;
use Dnw\Adjudicator\WebAdjudicatorImplementation;
use Dnw\Game\Application\Query\GetAllVariants\GetAllVariantsQueryHandlerInterface;
use Dnw\Game\Application\Query\GetAllVariants\LaravelGetAllVariantsQueryHandler;
use Dnw\Game\Application\Query\GetNewGames\GetNewGamesLaravelQueryHandler;
use Dnw\Game\Application\Query\GetNewGames\GetNewGamesQueryHandlerInterface;
use Dnw\Game\Domain\Adapter\RandomNumberGenerator\RandomNumberGenerator;
use Dnw\Game\Domain\Adapter\RandomNumberGenerator\RandomNumberGeneratorInterface;
use Dnw\Game\Domain\Adapter\TimeProvider\LaravelTimeProvider;
use Dnw\Game\Domain\Adapter\TimeProvider\TimeProviderInterface;
use Dnw\Game\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Domain\Game\Repository\Game\Impl\Laravel\LaravelGameRepository;
use Dnw\Game\Domain\Game\Repository\Phase\Impl\Laravel\LaravelPhaseRepository;
use Dnw\Game\Domain\Game\Repository\Phase\PhaseRepositoryInterface;
use Dnw\Game\Domain\Player\Repository\Player\Impl\LaravelPlayerRepository;
use Dnw\Game\Domain\Player\Repository\Player\PlayerRepositoryInterface;
use Dnw\Game\Domain\Variant\Repository\Impl\LaravelVariantRepository\LaravelVariantRepository;
use Dnw\Game\Domain\Variant\Repository\VariantRepositoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Storage;

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
        TimeProviderInterface::class => LaravelTimeProvider::class,
        RandomNumberGeneratorInterface::class => RandomNumberGenerator::class,
        GetAllVariantsQueryHandlerInterface::class => LaravelGetAllVariantsQueryHandler::class,
        GetNewGamesQueryHandlerInterface::class => GetNewGamesLaravelQueryHandler::class,
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

        $this->app->bind(PhaseRepositoryInterface::class, function (Application $app) {
            return new LaravelPhaseRepository(
                Storage::disk('public'),
            );
        });

    }
}
