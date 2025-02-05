<?php

namespace Dnw\Game\Domain\Player;

use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Player::class)]
class PlayerTest extends TestCase
{
    public function test_canParticipateInAnotherGame_passes_at_most_three_games(): void
    {
        $player = new Player(PlayerId::new(), 0);
        $this->assertTrue($player->canParticipateInAnotherGame()->passes());

        $player = new Player(PlayerId::new(), 3);
        $this->assertTrue($player->canParticipateInAnotherGame()->passes());
    }

    public function test_canParticipateInAnotherGame_fails_at_four_or_more_games(): void
    {
        $player = new Player(PlayerId::new(), 4);
        $this->assertTrue($player->canParticipateInAnotherGame()->fails());

        $player = new Player(PlayerId::new(), 400);
        $this->assertTrue($player->canParticipateInAnotherGame()->fails());
    }
}
