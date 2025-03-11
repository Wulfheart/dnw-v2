<?php

namespace Dnw\Game\Application\Query\GetGameIdByName;

use Dnw\Foundation\Identity\Id;
use Wulfheart\Option\Result;

/**
 * @extends Result<Id, self::E_*>
 */
class GetGameIdByNameQueryResult extends Result
{
    public const string E_GAME_NOT_FOUND = 'game_not_found';
}
