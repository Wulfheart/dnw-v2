<?php

namespace Dnw\Game\Domain\Game\Repository\Game\Impl\Laravel;

use Dnw\Foundation\Event\EventDispatcherInterface;
use Dnw\Game\Domain\Game\Repository\Game\AbstractGameRepositoryTestCase;
use Dnw\Game\Domain\Game\Repository\Game\GameRepositoryInterface;
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
