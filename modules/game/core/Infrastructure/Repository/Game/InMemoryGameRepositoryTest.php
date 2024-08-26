<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Game;

use Dnw\Foundation\Event\EventDispatcherInterface;
use Dnw\Game\Core\Domain\Game\Repository\Game\AbstractGameRepositoryTestCase;
use Dnw\Game\Core\Domain\Game\Repository\Game\GameRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InMemoryGameRepository::class)]
class InMemoryGameRepositoryTest extends AbstractGameRepositoryTestCase
{
    public function buildRepository(EventDispatcherInterface $eventDispatcher): GameRepositoryInterface
    {
        return new InMemoryGameRepository($eventDispatcher);
    }
}