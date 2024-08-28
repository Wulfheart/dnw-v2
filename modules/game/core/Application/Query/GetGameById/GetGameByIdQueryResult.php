<?php

namespace Dnw\Game\Core\Application\Query\GetGameById;

use Std\Result;

/**
 * @extends Result<GetGameByIdQueryResultData, self::E_*>
 */
class GetGameByIdQueryResult extends Result
{
    public const string E_GAME_NOT_FOUND = 'game_not_found';
}
