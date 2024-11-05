<?php

namespace Dnw\Game\Core\Application\Command\AdjudicateGame;

use Dnw\Adjudicator\Dto\AdjudicateGameResponse;
use Dnw\Adjudicator\FakeAdjudicatorService;
use Dnw\Foundation\Event\FakeEventDispatcher;
use Dnw\Game\Core\Domain\Adapter\TimeProvider\FakeTimeProvider;
use Dnw\Game\Core\Domain\Game\Test\Asserter\GameAsserter;
use Dnw\Game\Core\Domain\Game\Test\Factory\GameBuilder;
use Dnw\Game\Core\Domain\Game\Test\Factory\VariantFactory;
use Dnw\Game\Core\Infrastructure\Repository\Game\InMemoryGameRepository;
use Dnw\Game\Core\Infrastructure\Repository\Phase\InMemoryPhaseRepository;
use Dnw\Game\Core\Infrastructure\Repository\Variant\InMemoryVariantRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

#[CoversClass(AdjudicateGameCommand::class)]
#[CoversClass(AdjudicateGameCommandHandler::class)]
class AdjudicateGameCommandHandlerTest extends TestCase
{
    private const string NEW_ENCODED_STATE = '::NEW_ENCODED_STATE::';

    private const string SVG_ADJUDICATED = '::SVG_ADJUDICATED::';

    private const string SVG_WITH_ORDERS = '::SVG_WITH_ORDERS::';

    public function test_handle(): void
    {
        $variant = VariantFactory::standard();
        $game = GameBuilder::initialize(variant: $variant)->storeInitialAdjudication()->start()->submitOrders(true)->build();

        $stateEncoded = '::ENCODED_STATE::';

        $adjudicateGameResponse = AdjudicateGameResponse::fromArray(
            json_decode(
                // @phpstan-ignore argument.type
                file_get_contents(
                    __DIR__ . '/Test/Fixture/adjudicateGameResponse.json'
                ),
                true
            )
        );

        $gameRepository = new InMemoryGameRepository(
            new FakeEventDispatcher(),
            [$game]
        );
        $adjudicatorService = new FakeAdjudicatorService(
            adjudicateGameResponses: [$stateEncoded => $adjudicateGameResponse]
        );
        $variantRepository = new InMemoryVariantRepository([
            $variant,
        ]);

        $previousPhaseId = $game->phasesInfo->currentPhase->unwrap()->phaseId;
        $phaseRepository = new InMemoryPhaseRepository(
            [(string) $previousPhaseId => $stateEncoded]
        );

        $timeProvider = new FakeTimeProvider('2021-01-01 00:00:00');

        $handler = new AdjudicateGameCommandHandler(
            $adjudicatorService,
            $gameRepository,
            $variantRepository,
            $phaseRepository,
            $timeProvider,
            new NullLogger()
        );

        $command = new AdjudicateGameCommand($game->gameId->toId());

        $handler->handle($command);

        $gameToPerformAssertionsOn = $gameRepository->load($game->gameId)->unwrap();
        GameAsserter::assertThat($gameToPerformAssertionsOn)
            ->hasNotCurrentPhaseId($previousPhaseId);

        $currentPhaseId = $gameToPerformAssertionsOn->phasesInfo->currentPhase->unwrap()->phaseId;
        $newEncodedState = $phaseRepository->loadEncodedState($currentPhaseId)->unwrap();
        $this->assertEquals(self::NEW_ENCODED_STATE, $newEncodedState);

        $this->assertEquals(self::SVG_WITH_ORDERS, $phaseRepository->loadSvgWithOrders($previousPhaseId)->unwrap());
        $this->assertEquals(self::SVG_ADJUDICATED, $phaseRepository->loadAdjudicatedSvg($currentPhaseId)->unwrap());
    }
}
