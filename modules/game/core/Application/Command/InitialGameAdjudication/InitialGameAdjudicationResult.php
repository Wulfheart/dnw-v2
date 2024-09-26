<?php

namespace Dnw\Game\Core\Application\Command\InitialGameAdjudication;

use Wulfeart\Option\Result;

/**
 * @extends Result<void, self::E_*>
 */
class InitialGameAdjudicationResult extends Result
{
    public const string E_GAME_NOT_FOUND = 'game_not_found';

    public const string E_VARIANT_NOT_FOUND = 'variant_not_found';
}
