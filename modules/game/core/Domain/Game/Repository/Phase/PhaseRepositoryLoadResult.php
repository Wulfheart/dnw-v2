<?php

namespace Dnw\Game\Core\Domain\Game\Repository\Phase;

use Wulfeart\Option\Result;

/**
 * @extends Result<string, self::E_*>
 */
class PhaseRepositoryLoadResult extends Result
{
    public const string E_NOT_FOUND = 'not_found';
}
