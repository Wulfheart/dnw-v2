<?php

namespace Dnw\Game\Core\Domain\Game\Repository;

use Dnw\Game\Core\Domain\Game\Aggregate\Game;
use Dnw\Game\Core\Domain\Game\ValueObject\Game\GameId;

interface GameRepositoryInterface
{
    /**
     * @throw NotFoundException
     */
    public function load(GameId $gameId): Game;

    public function save(Game $game): void;
}
