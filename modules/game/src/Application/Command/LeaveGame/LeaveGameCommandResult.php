<?php

namespace Dnw\Game\Application\Command\LeaveGame;

use Wulfheart\Option\Result;

/**
 * @extends Result<void, self::E_*>
 */
class LeaveGameCommandResult extends Result
{
    public const string E_GAME_NOT_FOUND = 'game_not_found';
}
