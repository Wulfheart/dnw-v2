<?php

namespace Dnw\Game\Domain\Player;

use Dnw\Foundation\Rule\Rule;
use Dnw\Foundation\Rule\Ruleset;
use Dnw\Game\Domain\Player\Rule\PlayerRules;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;

readonly class Player
{
    public function __construct(
        public PlayerId $playerId,
        public int $numberOfCurrentlyPlayingGames,
    ) {}

    public function canParticipateInAnotherGame(): Ruleset
    {
        return new Ruleset(
            new Rule(
                PlayerRules::CURRENTLY_IN_TOO_MANY_GAMES,
                $this->numberOfCurrentlyPlayingGames >= 3
            )
        );
    }
}
