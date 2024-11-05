<?php

namespace Dnw\Game\Domain\Game\Repository\Game;

use Dnw\Game\Domain\Game\Game;
use Dnw\Game\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Domain\Game\ValueObject\Game\GameName;
use Wulfheart\Option\Option;

interface GameRepositoryInterface
{
    public function load(GameId $gameId): LoadGameResult;

    public function save(Game $game): void;

    /**
     * @return Option<GameId>
     */
    public function getGameIdByName(GameName $name): Option;
}
