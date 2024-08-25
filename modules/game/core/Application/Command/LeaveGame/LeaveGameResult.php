<?php

namespace Dnw\Game\Core\Application\Command\LeaveGame;

use Std\Result;

/**
 * @extends Result<void, self::E_*>
 */
class LeaveGameResult extends Result
{
    public const E_GAME_NOT_FOUND = 'game_not_found';
}
