<?php

namespace Dnw\Game\Core\Application\Command\LeaveGame;

use Dnw\Foundation\Event\FakeEventDispatcher;
use Dnw\Game\Core\Domain\Game\Test\Asserter\GameAsserter;
use Dnw\Game\Core\Domain\Game\Test\Factory\GameBuilder;
use Dnw\Game\Core\Infrastructure\Repository\Game\InMemoryGameRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

#[CoversClass(LeaveGameCommand::class)]
#[CoversClass(LeaveGameCommandHandler::class)]
class LeaveGameCommandHandlerTest extends TestCase
{
    public function test_handle(): void
    {
        $game = GameBuilder::initialize(true)->storeInitialAdjudication()->join()->build();
        $playerToRemove = $game->powerCollection->getAssignedPowers()->first()->playerId->unwrap();

        $gameRepository = new InMemoryGameRepository(new FakeEventDispatcher(), [$game]);

        $handler = new LeaveGameCommandHandler($gameRepository, new NullLogger());

        $command = new LeaveGameCommand($game->gameId->toId(), $playerToRemove->toId());
        $handler->handle($command);

        $game = $gameRepository->load($game->gameId)->unwrap();
        GameAsserter::assertThat($game)
            ->hasPlayerNotInGame($playerToRemove);
    }
}