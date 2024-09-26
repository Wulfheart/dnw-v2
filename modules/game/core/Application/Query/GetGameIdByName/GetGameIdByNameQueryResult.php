<?php

namespace Dnw\Game\Core\Application\Query\GetGameIdByName;

use Wulfeart\Option\Result;

/**
 * @extends Result<string, self::E_*>
 */
class GetGameIdByNameQueryResult extends Result
{
    public const E_GAME_NOT_FOUND = 'game_not_found';
}
