<?php

namespace Dnw\Game\Domain\Game\Repository\Phase;

use Wulfheart\Option\Result;

/**
 * @extends Result<null, self::E_*>
 */
final class PhaseRepositorySaveResult extends Result
{
    public const string E_ALREADY_PRESENT = 'already_present';
}
