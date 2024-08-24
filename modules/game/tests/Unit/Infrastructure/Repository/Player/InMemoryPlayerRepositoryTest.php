<?php

namespace Dnw\Game\Tests\Unit\Infrastructure\Repository\Player;

use Dnw\Game\Core\Domain\Player\Player;
use Dnw\Game\Core\Domain\Player\ValueObject\PlayerId;
use Dnw\Game\Core\Infrastructure\Repository\Player\InMemoryPlayerRepository;
use Dnw\Game\Tests\Unit\Domain\Player\Repository\AbstractPlayerRepositoryTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InMemoryPlayerRepository::class)]
class InMemoryPlayerRepositoryTest extends AbstractPlayerRepositoryTestCase
{
    protected function buildRepository(): array
    {
        $players = [
            new Player(PlayerId::new(), 0),
            new Player(PlayerId::new(), 4),
            new Player(PlayerId::new(), 2),
            new Player(PlayerId::new(), 10),
        ];
        $index = [];
        foreach ($players as $player) {
            $index[(string) $player->playerId] = $player;
        }

        return [
            new InMemoryPlayerRepository($index),
            $players,
        ];
    }
}
