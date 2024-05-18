<?php

namespace Dnw\Game\Tests\Unit\Application\Command;

use Dnw\Adjudicator\Dto\AdjudicateGameResponse;
use Dnw\Adjudicator\FakeAdjudicatorService;
use Dnw\Foundation\Event\FakeEventDispatcher;
use Dnw\Game\Core\Application\Command\InitialGameAdjudication\InitialGameAdjudicationCommand;
use Dnw\Game\Core\Application\Command\InitialGameAdjudication\InitialGameAdjudicationCommandHandler;
use Dnw\Game\Core\Domain\Adapter\TimeProvider\FakeTimeProvider;
use Dnw\Game\Core\Infrastructure\Repository\Game\InMemoryGameRepository;
use Dnw\Game\Core\Infrastructure\Repository\Phase\InMemoryPhaseRepository;
use Dnw\Game\Core\Infrastructure\Repository\Variant\InMemoryVariantRepository;
use Dnw\Game\Tests\Factory\GameBuilder;
use Dnw\Game\Tests\Factory\VariantFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(InitialGameAdjudicationCommand::class)]
#[CoversClass(InitialGameAdjudicationCommandHandler::class)]
class InitialGameAdjudicationTest extends TestCase
{
    private const string NEW_ENCODED_STATE = '::NEW_ENCODED_STATE::';

    private const string SVG_ADJUDICATED = '::SVG_ADJUDICATED::';

    public function test_handle(): void
    {
        $variant = VariantFactory::standard();
        $game = GameBuilder::initialize(variant: $variant)->build();

        $stateEncoded = '::ENCODED_STATE::';

        $adjudicateGameResponse = AdjudicateGameResponse::fromArray(
            json_decode(
                // @phpstan-ignore argument.type
                file_get_contents(
                    __DIR__ . '/Fixture/initialAdjudicationGameResponse.json'
                ),
                true
            )
        );

        $gameRepository = new InMemoryGameRepository(
            new FakeEventDispatcher(),
            [$game]
        );
        $adjudicatorService = new FakeAdjudicatorService(
            initializeGameResponses: [(string) $variant->apiName => $adjudicateGameResponse]
        );
        $variantRepository = new InMemoryVariantRepository([$variant]);

        $phaseRepository = new InMemoryPhaseRepository();

        $timeProvider = new FakeTimeProvider('2021-01-01 00:00:00');

        $handler = new InitialGameAdjudicationCommandHandler(
            $adjudicatorService,
            $gameRepository,
            $phaseRepository,
            $variantRepository,
            $timeProvider
        );

        $command = new InitialGameAdjudicationCommand($game->gameId->toId());

        $handler->handle($command);

        $gameToPerformAssertionsOn = $gameRepository->load($game->gameId);

        $currentPhaseId = $gameToPerformAssertionsOn->phasesInfo->currentPhase->unwrap()->phaseId;
        $newEncodedState = $phaseRepository->loadEncodedState($currentPhaseId);
        $this->assertEquals(self::NEW_ENCODED_STATE, $newEncodedState);

        $this->assertEquals(self::SVG_ADJUDICATED, $phaseRepository->loadAdjudicatedSvg($currentPhaseId));
    }
}
