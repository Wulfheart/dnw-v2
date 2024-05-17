<?php

namespace Dnw\Game\Tests\Unit\Application\Command;

use Dnw\Foundation\Event\FakeEventDispatcher;
use Dnw\Game\Core\Application\Command\LeaveGame\LeaveGameCommand;
use Dnw\Game\Core\Application\Command\LeaveGame\LeaveGameCommandHandler;
use Dnw\Game\Core\Infrastructure\Repository\Game\InMemoryGameRepository;
use Dnw\Game\Tests\Asserter\GameAsserter;
use Dnw\Game\Tests\Factory\GameBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LeaveGameCommand::class)]
#[CoversClass(LeaveGameCommandHandler::class)]
class LeaveGameTest extends TestCase
{
    public function test_handle(): void
    {
        $game = GameBuilder::initialize(true)->storeInitialAdjudication()->join()->build();
        $playerToRemove = $game->powerCollection->getAssignedPowers()->first()->playerId->get();

        $gameRepository = new InMemoryGameRepository(new FakeEventDispatcher(), [$game]);

        $handler = new LeaveGameCommandHandler($gameRepository);

        $command = new LeaveGameCommand($game->gameId->toId(), $playerToRemove->toId());
        $handler->handle($command);

        $game = $gameRepository->load($game->gameId);
        GameAsserter::assertThat($game)
            ->hasPlayerNotInGame($playerToRemove);
    }
}
