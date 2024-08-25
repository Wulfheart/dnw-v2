<?php

namespace Dnw\Game\Core\Domain\Game\Repository\Game;

use Dnw\Game\Core\Domain\Game\Game;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;

interface GameRepositoryInterface
{
    public function load(GameId $gameId): LoadGameResult;

    public function save(Game $game): void;
}
