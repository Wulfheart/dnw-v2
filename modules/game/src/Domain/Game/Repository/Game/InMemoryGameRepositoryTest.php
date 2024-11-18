<?php

namespace Dnw\Game\Domain\Game\Repository\Game;

use Dnw\Foundation\Event\EventDispatcherInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InMemoryGameRepository::class)]
class InMemoryGameRepositoryTest extends AbstractGameRepositoryTestCase
{
    public function buildRepository(EventDispatcherInterface $eventDispatcher): GameRepositoryInterface
    {
        return new InMemoryGameRepository($eventDispatcher);
    }
}
