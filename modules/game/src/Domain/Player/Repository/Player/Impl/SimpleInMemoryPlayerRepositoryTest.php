<?php

namespace Dnw\Game\Domain\Player\Repository\Player\Impl;

use Dnw\Game\Domain\Player\Player;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SimpleInMemoryPlayerRepository::class)]
class SimpleInMemoryPlayerRepositoryTest extends TestCase
{
    public function test_non_existent_player_has_zero_games(): void
    {
        $repo = new SimpleInMemoryPlayerRepository();
        $player = $repo->load(PlayerId::new());
        $this->assertEquals(0, $player->unwrap()->numberOfCurrentlyPlayingGames);
    }

    public function test_existing_player_has_correct_number_of_games(): void
    {
        $playerId = PlayerId::new();
        $repo = new SimpleInMemoryPlayerRepository([
            new Player($playerId, 4),
            new Player(PlayerId::new(), 2),
            new Player(PlayerId::new(), 0),
        ]);
        $player = $repo->load($playerId->clone());
        $this->assertEquals(4, $player->unwrap()->numberOfCurrentlyPlayingGames);
    }
}
