<?php

namespace Dnw\Game\Infrastructure\Repository\Player;

use Dnw\Game\Domain\Player\Player;
use Dnw\Game\Domain\Player\Repository\Player\LoadPlayerResult;
use Dnw\Game\Domain\Player\Repository\Player\PlayerRepositoryInterface;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;

final readonly class SimpleInMemoryPlayerRepository implements PlayerRepositoryInterface
{
    /**
     * @param  array<Player>  $players
     */
    public function __construct(
        private array $players = []
    ) {}

    public function load(PlayerId $playerId): LoadPlayerResult
    {
        foreach ($this->players as $player) {
            if ($player->playerId == $playerId) {
                return LoadPlayerResult::ok($player);
            }
        }

        $player = new Player($playerId, 0);

        return LoadPlayerResult::ok($player);
    }
}
