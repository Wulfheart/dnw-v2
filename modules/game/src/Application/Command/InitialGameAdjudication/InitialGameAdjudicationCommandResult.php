<?php

namespace Dnw\Game\Application\Command\InitialGameAdjudication;

use Wulfheart\Option\Result;

/**
 * @extends Result<void, self::E_*>
 */
class InitialGameAdjudicationCommandResult extends Result
{
    public const string E_GAME_NOT_FOUND = 'game_not_found';

    public const string E_VARIANT_NOT_FOUND = 'variant_not_found';
}