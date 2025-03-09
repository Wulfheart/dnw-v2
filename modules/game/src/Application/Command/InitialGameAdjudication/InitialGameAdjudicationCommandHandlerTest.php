<?php

namespace Dnw\Game\Application\Command\InitialGameAdjudication;

use Dnw\Adjudicator\Dto\AdjudicateGameResponse;
use Dnw\Adjudicator\FakeAdjudicatorService;
use Dnw\Foundation\Event\FakeEventDispatcher;
use Dnw\Game\Domain\Adapter\TimeProvider\FakeTimeProvider;
use Dnw\Game\Domain\Game\Repository\Game\Impl\InMemory\InMemoryGameRepository;
use Dnw\Game\Domain\Game\Repository\Phase\Impl\InMemory\InMemoryPhaseRepository;
use Dnw\Game\Domain\Game\Test\Factory\GameBuilder;
use Dnw\Game\Domain\Game\Test\Factory\VariantFactory;
use Dnw\Game\Domain\Variant\Repository\Impl\InMemory\InMemoryVariantRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

#[CoversClass(InitialGameAdjudicationCommand::class)]
#[CoversClass(InitialGameAdjudicationCommandHandler::class)]
class InitialGameAdjudicationCommandHandlerTest extends TestCase
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
                    __DIR__ . '/Test/Fixture/initialAdjudicationGameResponse.json'
                ),
                true
            )
        );

        $gameRepository = new InMemoryGameRepository(
            new FakeEventDispatcher(),
            [$game]
        );
        $adjudicatorService = new FakeAdjudicatorService(
            initializeGameResponses: [(string) $variant->key => $adjudicateGameResponse]
        );
        $variantRepository = new InMemoryVariantRepository([$variant]);

        $phaseRepository = new InMemoryPhaseRepository();

        $timeProvider = new FakeTimeProvider('2021-01-01 00:00:00');

        $handler = new InitialGameAdjudicationCommandHandler(
            $adjudicatorService,
            $gameRepository,
            $phaseRepository,
            $variantRepository,
            $timeProvider,
            new NullLogger()
        );

        $command = new InitialGameAdjudicationCommand($game->gameId->toId());

        $handler->handle($command);

        $gameToPerformAssertionsOn = $gameRepository->load($game->gameId)->unwrap();

        $currentPhaseId = $gameToPerformAssertionsOn->phasesInfo->currentPhase->unwrap()->phaseId;
        $newEncodedState = $phaseRepository->loadEncodedState($currentPhaseId);
        $this->assertEquals(self::NEW_ENCODED_STATE, $newEncodedState->unwrap());

        $this->assertEquals(self::SVG_ADJUDICATED, $phaseRepository->loadAdjudicatedSvg($currentPhaseId)->unwrap());
    }
}
