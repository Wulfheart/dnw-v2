<?php

namespace Dnw\Game\Application\Command\JoinGame;

use Wulfheart\Option\Result;

/**
 * @extends Result<void, self::E_*>
 */
class JoinGameResult extends Result
{
    public const E_GAME_NOT_FOUND = 'game_not_found';
}
