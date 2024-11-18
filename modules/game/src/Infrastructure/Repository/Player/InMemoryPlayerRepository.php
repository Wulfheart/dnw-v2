<?php

namespace Dnw\Game\Infrastructure\Repository\Player;

use Dnw\Game\Domain\Game\Repository\Game\InMemoryGameRepository;
use Dnw\Game\Domain\Game\StateMachine\GameStates;
use Dnw\Game\Domain\Player\Player;
use Dnw\Game\Domain\Player\Repository\Player\PlayerRepositoryInterface;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;

class InMemoryPlayerRepository implements PlayerRepositoryInterface
{
    public function __construct(
        private InMemoryGameRepository $gameRepository,
    ) {}

    public function load(PlayerId $playerId): Player
    {
        $games = $this->gameRepository->getAllGames();
        $count = 0;
        foreach ($games as $game) {
            if ($game->powerCollection->containsPlayer($playerId)
                && $game->gameStateMachine->currentStateIsNot(GameStates::FINISHED)) {
                $count++;
            }
        }

        return new Player($playerId, $count);
    }
}
