<?php

namespace Dnw\Game\Infrastructure\Repository\Player;

use Dnw\Foundation\Event\FakeEventDispatcher;
use Dnw\Game\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Domain\Game\Repository\Game\InMemoryGameRepository;
use Dnw\Game\Domain\Player\Repository\Player\AbstractPlayerRepositoryTestCase;
use Dnw\Game\Domain\Player\Repository\Player\PlayerRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InMemoryPlayerRepository::class)]
class InMemoryPlayerRepositoryTest extends AbstractPlayerRepositoryTestCase
{
    protected function buildPlayerRepo(): PlayerRepositoryInterface
    {
        return new InMemoryPlayerRepository($this->buildInMemoryGameRepo());
    }

    protected function buildGameRepo(): GameRepositoryInterface
    {
        return $this->buildInMemoryGameRepo();
    }

    private function buildInMemoryGameRepo(): InMemoryGameRepository
    {
        static $repo;
        if (! $repo) {
            $repo = new InMemoryGameRepository(new FakeEventDispatcher());
        }

        return $repo;
    }
}
