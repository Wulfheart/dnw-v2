<?php

namespace Dnw\Game\Core\Application\Command\SubmitOrders;

use Wulfheart\Option\Result;

/**
 * @extends Result<void, self::E_*>
 */
class SubmitOrdersResult extends Result
{
    public const E_GAME_NOT_FOUND = 'game_not_found';
}
