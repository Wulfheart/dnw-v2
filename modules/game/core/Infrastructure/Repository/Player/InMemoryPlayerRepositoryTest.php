<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Player;

use Dnw\Foundation\Event\FakeEventDispatcher;
use Dnw\Game\Core\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Player\Repository\Player\AbstractPlayerRepositoryTestCase;
use Dnw\Game\Core\Domain\Player\Repository\Player\PlayerRepositoryInterface;
use Dnw\Game\Core\Infrastructure\Repository\Game\InMemoryGameRepository;
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
