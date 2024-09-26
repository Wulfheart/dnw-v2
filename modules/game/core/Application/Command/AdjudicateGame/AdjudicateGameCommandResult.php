<?php

namespace Dnw\Game\Core\Application\Command\AdjudicateGame;

use Wulfeart\Option\Result;

/**
 * @extends Result<void, self::E_>
 */
class AdjudicateGameCommandResult extends Result
{
    public const E_GAME_NOT_FOUND = 'game_not_found';

    public const E_VARIANT_NOT_FOUND = 'variant_not_found';
}
