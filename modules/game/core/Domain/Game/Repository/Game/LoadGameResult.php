<?php

namespace Dnw\Game\Core\Domain\Game\Repository\Game;

use Dnw\Game\Core\Domain\Game\Game;
use Wulfheart\Option\Result;

/**
 * @extends Result<Game, self::E_>
 */
class LoadGameResult extends Result
{
    public const E_GAME_NOT_FOUND = 'game_not_found';
}
