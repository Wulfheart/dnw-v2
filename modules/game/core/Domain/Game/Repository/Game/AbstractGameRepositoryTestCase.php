<?php

namespace Dnw\Game\Core\Domain\Game\Repository\Game;

use Dnw\Foundation\Event\EventDispatcherInterface;
use Dnw\Foundation\Event\FakeEventDispatcher;
use Dnw\Foundation\PHPStan\AllowLaravelTest;
use Dnw\Game\Core\Domain\Game\Event\GameCreatedEvent;
use Dnw\Game\Core\Domain\Game\Test\Factory\GameBuilder;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Tests\TestCase;

#[AllowLaravelTest]
abstract class AbstractGameRepositoryTestCase extends TestCase
{
    abstract public function buildRepository(EventDispatcherInterface $eventDispatcher): GameRepositoryInterface;

    public function test_can_load(): void
    {
        $game = GameBuilder::initialize()->doNotReleaseEvents()->build();
        $eventDispatcher = new FakeEventDispatcher();
        $repository = $this->buildRepository($eventDispatcher);

        $repository->save($game);

        $loadedGame = $repository->load($game->gameId)->unwrap();

        $this->assertEquals($game, $loadedGame);
        $eventDispatcher->assertDispatched(GameCreatedEvent::class, 1);
    }

    public function test_advanced_mid_game(): void
    {
        $game = GameBuilder::initialize()
            ->storeInitialAdjudication()
            ->start()
            ->submitOrders(true)
            ->defeatPower()
            ->build();
        $repository = $this->buildRepository(new FakeEventDispatcher());

        $repository->save($game);

        $loadedGame = $repository->load($game->gameId)->unwrap();

        $this->assertEquals($game, $loadedGame);
    }

    public function test_errors_if_cannot_load(): void
    {
        $eventDispatcher = new FakeEventDispatcher();

        $repository = $this->buildRepository($eventDispatcher);
        $result = $repository->load(GameId::new());
        $this->assertTrue($result->hasErr());
        $this->assertEquals(LoadGameResult::E_GAME_NOT_FOUND, $result->unwrapErr());
    }
}
