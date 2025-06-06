<?php

namespace Dnw\Game\Application\Command\JoinGame;

use Dnw\Foundation\Event\FakeEventDispatcher;
use Dnw\Foundation\Identity\Id;
use Dnw\Game\Domain\Adapter\RandomNumberGenerator\FakeRandomNumberGenerator;
use Dnw\Game\Domain\Adapter\TimeProvider\FakeTimeProvider;
use Dnw\Game\Domain\Game\Repository\Game\Impl\InMemory\InMemoryGameRepository;
use Dnw\Game\Domain\Game\Test\Asserter\GameAsserter;
use Dnw\Game\Domain\Game\Test\Factory\GameBuilder;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Wulfheart\Option\Option;

#[CoversClass(JoinGameCommand::class)]
#[CoversClass(JoinGameCommandHandler::class)]
class JoinGameCommandHandlerTest extends TestCase
{
    public function test_handle(): void
    {
        $game = GameBuilder::initialize(true)->storeInitialAdjudication()->build();

        $gameRepository = new InMemoryGameRepository(
            new FakeEventDispatcher(),
            [$game]
        );
        $timeProvider = new FakeTimeProvider('2021-01-01 00:00:00');
        $randomNumberGenerator = new FakeRandomNumberGenerator(2);

        $handler = new JoinGameCommandHandler(
            $gameRepository,
            $timeProvider,
            $randomNumberGenerator,
            new NullLogger()
        );

        $userId = Id::generate();

        $command = new JoinGameCommand(
            $game->gameId->toId(),
            $userId,
            Option::none(),
        );

        $handler->handle($command);

        $game = $gameRepository->load($game->gameId)->unwrap();
        GameAsserter::assertThat($game)
            ->hasPlayerInGame(PlayerId::fromId($userId));

    }
}
