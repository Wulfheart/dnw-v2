<?php

namespace Dnw\Game\Infrastructure\Repository\Player;

use Dnw\Game\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Domain\Player\Repository\Player\AbstractPlayerRepositoryTestCase;
use Dnw\Game\Domain\Player\Repository\Player\PlayerRepositoryInterface;
use Dnw\Game\Infrastructure\Repository\Game\LaravelGameRepository;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LaravelPlayerRepository::class)]
class LaravelPlayerRepositoryTest extends AbstractPlayerRepositoryTestCase
{
    protected function buildPlayerRepo(): PlayerRepositoryInterface
    {
        return $this->app->make(LaravelPlayerRepository::class);
    }

    protected function buildGameRepo(): GameRepositoryInterface
    {
        return $this->app->make(LaravelGameRepository::class);
    }
}
