<?php

namespace Dnw\Game\Tests\Unit\Infrastructure\Repository\Game;

use Dnw\Foundation\Event\EventDispatcherInterface;
use Dnw\Game\Core\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Core\Infrastructure\Repository\Game\InMemoryGameRepository;
use Dnw\Game\Tests\Unit\Domain\Game\Repository\AbstractGameRepositoryTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InMemoryGameRepository::class)]
class InMemoryGameRepositoryTest extends AbstractGameRepositoryTestCase
{
    public function buildRepository(EventDispatcherInterface $eventDispatcher): GameRepositoryInterface
    {
        return new InMemoryGameRepository($eventDispatcher);
    }
}
