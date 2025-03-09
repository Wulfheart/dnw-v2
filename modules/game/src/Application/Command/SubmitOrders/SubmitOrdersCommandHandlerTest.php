<?php

namespace Dnw\Game\Application\Command\SubmitOrders;

use Dnw\Foundation\Event\FakeEventDispatcher;
use Dnw\Game\Domain\Adapter\TimeProvider\FakeTimeProvider;
use Dnw\Game\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Domain\Game\Repository\Game\InMemoryGameRepository;
use Dnw\Game\Domain\Game\Test\Asserter\GameAsserter;
use Dnw\Game\Domain\Game\Test\Factory\GameBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Log\NullLogger;
use Tests\LaravelTestCase;

#[CoversClass(SubmitOrdersCommandHandler::class)]

class SubmitOrdersCommandHandlerTest extends LaravelTestCase
{
    public function test_handle(): void
    {
        $game = GameBuilder::initialize(true)->storeInitialAdjudication()->start()->build();

        $gameRepository = new InMemoryGameRepository(new FakeEventDispatcher(), [$game]);

        $power = $game->powerCollection->first();
        $timeProvider = new FakeTimeProvider('2021-01-01 12:00:00');

        $handler = new SubmitOrdersCommandHandler($gameRepository, $timeProvider, new NullLogger());
        $orders = ['A PAR - BUR', 'F LON - NTH'];

        $command = new SubmitOrdersCommand(
            $game->gameId->toId(),
            $power->playerId->unwrap()->toId(),
            true,
            $orders
        );

        $handler->handle($command);

        $game = $gameRepository->load($game->gameId)->unwrap();
        GameAsserter::assertThat($game)
            ->hasPowerWithOrders($power->powerId, OrderCollection::fromStringArray($orders));

    }
}
