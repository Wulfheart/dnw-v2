<?php

namespace Dnw\Game\Core\Domain\Repository;

use Dnw\Game\Core\Domain\Aggregate\Game;
use Dnw\Game\Core\Domain\ValueObject\Game\GameId;

interface GameRepositoryInterface
{
    public function load(GameId $gameId): Game;

    public function save(Game $game): void;
}
