<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Player;

use Dnw\Game\Core\Domain\Player\Player;
use Dnw\Game\Core\Domain\Player\Repository\Player\PlayerRepositoryInterface;
use Dnw\Game\Core\Domain\Player\ValueObject\PlayerId;

class InMemoryPlayerRepository implements PlayerRepositoryInterface
{
    public function __construct(
        /** @var array<string, Player> */
        private array $players = [],
    ) {}

    public function load(PlayerId $playerId): Player
    {
        $player = $this->players[(string) $playerId] ?? null;
        if ($player === null) {
            $player = new Player($playerId, 0);
            $this->players[(string) $playerId] = $player;
        }

        return $player;
    }
}
