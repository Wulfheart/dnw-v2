<?php

namespace Dnw\Game\Application\Command\SubmitOrders;

use Wulfheart\Option\Result;

/**
 * @extends Result<void, self::E_*>
 */
class SubmitOrdersCommandResult extends Result
{
    public const string E_GAME_NOT_FOUND = 'game_not_found';
}
