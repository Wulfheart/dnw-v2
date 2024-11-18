<?php

namespace Dnw\Game\Application\Command\CreateGame;

use Dnw\Foundation\Event\FakeEventDispatcher;
use Dnw\Game\Domain\Adapter\RandomNumberGenerator\FakeRandomNumberGenerator;
use Dnw\Game\Domain\Adapter\TimeProvider\FakeTimeProvider;
use Dnw\Game\Domain\Game\Repository\Game\InMemoryGameRepository;
use Dnw\Game\Domain\Game\Test\Asserter\GameAsserter;
use Dnw\Game\Domain\Game\Test\Factory\VariantFactory;
use Dnw\Game\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use Dnw\Game\Infrastructure\Repository\Player\InMemoryPlayerRepository;
use Dnw\Game\Infrastructure\Repository\Variant\InMemoryVariantRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Wulfheart\Option\Option;

#[CoversClass(CreateGameCommand::class)]
#[CoversClass(CreateGameCommandHandler::class)]
class CreateGameCommandHandlerTest extends TestCase
{
    public function test_handle(): void
    {
        $variant = VariantFactory::standard();
        $variantRepository = new InMemoryVariantRepository(
            [$variant]
        );

        $playerId = PlayerId::new();
        $gameId = GameId::new();

        $command = new CreateGameCommand(
            $gameId->toId(),
            'Das ist ein Spiel',
            60,
            5,
            true,
            $variant->id->toId(),
            true,
            Option::none(),
            true,
            true,
            [0],
            $playerId->toId()
        );

        $timeProvider = new FakeTimeProvider('2021-01-01 00:00:00');
        $gameRepository = new InMemoryGameRepository(
            new FakeEventDispatcher(),
            []
        );
        $playerRepository = new InMemoryPlayerRepository(
            $gameRepository
        );
        $randomNumberGenerator = new FakeRandomNumberGenerator(0);

        $logger = new NullLogger();

        $handler = new CreateGameCommandHandler(
            $variantRepository,
            $timeProvider,
            $gameRepository,
            $playerRepository,
            $randomNumberGenerator,
            $logger
        );

        $handler->handle($command);

        $game = $gameRepository->load($gameId)->unwrap();

        GameAsserter::assertThat($game)
            ->hasGameId($gameId)
            ->powerIdHasPlayerId($game->powerCollection->getOffset(0)->powerId, $playerId);

    }
}
