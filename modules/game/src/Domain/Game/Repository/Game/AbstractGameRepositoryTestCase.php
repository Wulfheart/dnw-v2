<?php

namespace Dnw\Game\Domain\Game\Repository\Game;

use Dnw\Foundation\Differ\Differ;
use Dnw\Foundation\Event\EventDispatcherInterface;
use Dnw\Foundation\Event\FakeEventDispatcher;
use Dnw\Foundation\PHPStan\AllowLaravelTestCase;
use Dnw\Game\Domain\Game\Event\GameCreatedEvent;
use Dnw\Game\Domain\Game\Test\Factory\GameBuilder;
use Dnw\Game\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use Tests\TestCase;

#[AllowLaravelTestCase]
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

        Differ::make($game, $loadedGame)->drop('version')->assertEquality();
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

        Differ::make($game, $loadedGame)->drop('version')->assertEquality();
    }

    public function test_errors_if_cannot_load(): void
    {
        $eventDispatcher = new FakeEventDispatcher();

        $repository = $this->buildRepository($eventDispatcher);
        $result = $repository->load(GameId::new());
        $this->assertTrue($result->hasErr());
        $this->assertEquals(LoadGameResult::E_GAME_NOT_FOUND, $result->unwrapErr());
    }

    public function test_can_increment_version_on_change(): void
    {
        $playerId = PlayerId::new();
        $game = GameBuilder::initialize(playerId: $playerId)->storeInitialAdjudication()->build();

        $eventDispatcher = new FakeEventDispatcher();
        $repo = $this->buildRepository($eventDispatcher);
        $repo->save($game);

        $game = $repo->load($game->gameId)->unwrap();
        $game->leave($playerId);

        $repo->save($game);

        $this->expectNotToPerformAssertions();
    }
}
