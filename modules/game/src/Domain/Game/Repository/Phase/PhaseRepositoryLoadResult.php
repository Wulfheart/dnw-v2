<?php

namespace Dnw\Game\Domain\Game\Repository\Phase;

use Wulfheart\Option\Result;

/**
 * @extends Result<string, self::E_*>
 */
class PhaseRepositoryLoadResult extends Result
{
    public const string E_NOT_FOUND = 'not_found';
}
