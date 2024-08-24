<?php

namespace Dnw\Game\Tests\Unit\Domain\Player\Repository;

use Dnw\Game\Core\Domain\Player\Player;
use Dnw\Game\Core\Domain\Player\Repository\Player\PlayerRepositoryInterface;
use Dnw\Game\Core\Domain\Player\ValueObject\PlayerId;
use Tests\TestCase;

abstract class AbstractPlayerRepositoryTestCase extends TestCase
{
    /**
     * @return array{0: PlayerRepositoryInterface, 1: array<Player>}
     */
    abstract protected function buildRepository(): array;

    public function test_load_defaults_to_zero_count(): void
    {
        [$repo, $_] = $this->buildRepository();
        $result = $repo->load(PlayerId::new());
        $this->assertEquals(0, $result->numberOfCurrentlyPlayingGames);
    }

    public function test_load(): void
    {
        [$repo, $players] = $this->buildRepository();
        foreach ($players as $expectedPlayer) {
            $result = $repo->load($expectedPlayer->playerId);
            $this->assertEquals($expectedPlayer, $result);
        }
    }
}
