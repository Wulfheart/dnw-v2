<?php

namespace Dnw\Game\Application\Command\JoinGame;

use Wulfheart\Option\Result;

/**
 * @extends Result<void, self::E_*>
 */
class JoinGameCommandResult extends Result
{
    public const E_GAME_NOT_FOUND = 'game_not_found';
}
