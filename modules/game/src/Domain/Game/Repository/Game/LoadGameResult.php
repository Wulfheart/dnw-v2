<?php

namespace Dnw\Game\Domain\Game\Repository\Game;

use Dnw\Game\Domain\Game\Game;
use Wulfheart\Option\Result;

/**
 * @extends Result<Game, self::E_*>
 */
class LoadGameResult extends Result
{
    public const string E_GAME_NOT_FOUND = 'game_not_found';
}
