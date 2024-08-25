<?php

namespace Dnw\Game\Core\Application\Command\JoinGame;

use Std\Result;

/**
 * @extends Result<void, self::E_*>
 */
class JoinGameResult extends Result
{
    public const E_GAME_NOT_FOUND = 'game_not_found';
}
