<?php

namespace Dnw\Game\Domain\Game\Repository\Game\Impl\InMemory;

use Dnw\Foundation\Event\EventDispatcherInterface;
use Dnw\Game\Domain\Game\Repository\Game\AbstractGameRepositoryTestCase;
use Dnw\Game\Domain\Game\Repository\Game\GameRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InMemoryGameRepository::class)]
class InMemoryGameRepositoryTest extends AbstractGameRepositoryTestCase
{
    public function buildRepository(EventDispatcherInterface $eventDispatcher): GameRepositoryInterface
    {
        return new InMemoryGameRepository($eventDispatcher);
    }
}
