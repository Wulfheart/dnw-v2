<?php

namespace Dnw\Game\Core\Application\Query\GetGame;

use Std\Result;

/**
 * @extends Result<GetGameQueryResultData, self::E_*>
 */
class GetGameQueryResult extends Result
{
    public const E_GAME_NOT_FOUND = 'game_not_found';
}
