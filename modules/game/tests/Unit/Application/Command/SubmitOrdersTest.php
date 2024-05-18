<?php

namespace Dnw\Game\Tests\Unit\Application\Command;

use Dnw\Foundation\Event\FakeEventDispatcher;
use Dnw\Game\Core\Application\Command\SubmitOrders\SubmitOrdersCommand;
use Dnw\Game\Core\Application\Command\SubmitOrders\SubmitOrdersCommandHandler;
use Dnw\Game\Core\Domain\Adapter\TimeProvider\FakeTimeProvider;
use Dnw\Game\Core\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Core\Infrastructure\Repository\Game\InMemoryGameRepository;
use Dnw\Game\Tests\Asserter\GameAsserter;
use Dnw\Game\Tests\Factory\GameBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SubmitOrdersCommand::class)]
#[CoversClass(SubmitOrdersCommandHandler::class)]
class SubmitOrdersTest extends TestCase
{
    public function test_handle(): void
    {
        $game = GameBuilder::initialize(true)->storeInitialAdjudication()->start()->build();

        $gameRepository = new InMemoryGameRepository(new FakeEventDispatcher(), [$game]);

        $power = $game->powerCollection->first();
        $timeProvider = new FakeTimeProvider('2021-01-01 12:00:00');

        $handler = new SubmitOrdersCommandHandler($gameRepository, $timeProvider);
        $orders = ['A PAR - BUR', 'F LON - NTH'];

        $command = new SubmitOrdersCommand(
            $game->gameId->toId(),
            $power->playerId->unwrap()->toId(),
            true,
            $orders
        );

        $handler->handle($command);

        $game = $gameRepository->load($game->gameId);
        GameAsserter::assertThat($game)
            ->hasPowerWithOrders($power->powerId, OrderCollection::fromStringArray($orders));

    }
}
