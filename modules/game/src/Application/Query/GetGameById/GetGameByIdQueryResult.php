<?php

namespace Dnw\Game\Application\Query\GetGameById;

use Wulfheart\Option\Result;

/**
 * @extends Result<GetGameByIdQueryResultData, self::E_*>
 */
class GetGameByIdQueryResult extends Result
{
    public const string E_GAME_NOT_FOUND = 'game_not_found';
}
