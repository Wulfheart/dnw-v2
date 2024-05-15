<?php

namespace Dnw\Game\Tests\Unit\Domain\Game\Repository;

use Dnw\Foundation\Event\EventDispatcherInterface;
use Dnw\Foundation\Event\FakeEventDispatcher;
use Dnw\Game\Core\Domain\Game\Event\GameCreatedEvent;
use Dnw\Game\Core\Domain\Game\Exception\NotFoundException;
use Dnw\Game\Core\Domain\Game\Repository\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Tests\Mother\GameBuilder;
use Tests\TestCase;

abstract class AbstractGameRepositoryTestCase extends TestCase
{
    abstract public function buildRepository(EventDispatcherInterface $eventDispatcher): GameRepositoryInterface;

    public function test_can_load(): void
    {
        $game = GameBuilder::create()->build();
        $eventDispatcher = new FakeEventDispatcher();
        $repository = $this->buildRepository($eventDispatcher);

        $repository->save($game);

        $loadedGame = $repository->load($game->gameId);

        $this->assertSame($game, $loadedGame);
        $eventDispatcher->assertDispatched(GameCreatedEvent::class, 1);
    }

    public function test_errors_if_cannot_load(): void
    {
        $eventDispatcher = new FakeEventDispatcher();

        $repository = $this->buildRepository($eventDispatcher);
        $this->expectException(NotFoundException::class);
        $repository->load(GameId::generate());
    }
}
