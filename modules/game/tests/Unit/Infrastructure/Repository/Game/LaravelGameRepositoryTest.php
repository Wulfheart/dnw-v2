<?php

namespace Dnw\Game\Tests\Unit\Infrastructure\Repository\Game;

use Dnw\Foundation\Event\EventDispatcherInterface;
use Dnw\Game\Core\Domain\Game\Repository\GameRepositoryInterface;
use Dnw\Game\Core\Infrastructure\Repository\Game\LaravelGameRepository;
use Dnw\Game\Tests\Unit\Domain\Game\Repository\AbstractGameRepositoryTestCase;

class LaravelGameRepositoryTest extends AbstractGameRepositoryTestCase
{
    public function buildRepository(EventDispatcherInterface $eventDispatcher): GameRepositoryInterface
    {
        $this->app->bind(EventDispatcherInterface::class, fn () => $eventDispatcher);

        return $this->app->make(LaravelGameRepository::class);
    }
}
