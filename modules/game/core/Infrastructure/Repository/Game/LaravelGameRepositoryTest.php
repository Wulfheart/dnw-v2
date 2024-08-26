<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Game;

use Dnw\Foundation\Event\EventDispatcherInterface;
use Dnw\Game\Core\Domain\Game\Repository\Game\AbstractGameRepositoryTestCase;
use Dnw\Game\Core\Domain\Game\Repository\Game\GameRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LaravelGameRepository::class)]
class LaravelGameRepositoryTest extends AbstractGameRepositoryTestCase
{
    public function buildRepository(EventDispatcherInterface $eventDispatcher): GameRepositoryInterface
    {
        $this->app->bind(EventDispatcherInterface::class, fn () => $eventDispatcher);

        return $this->app->make(LaravelGameRepository::class);
    }
}
