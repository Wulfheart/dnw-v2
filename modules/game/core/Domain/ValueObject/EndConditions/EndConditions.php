<?php

namespace Dnw\Game\Core\Domain\ValueObject\EndConditions;

use Dnw\Game\Core\Domain\ValueObject\Count;
use PhpOption\Option;

class EndConditions
{
    public function __construct(
        /** @var Option<MaximumNumberOfRounds> $maximumNumberOfRounds */
        public Option $maximumNumberOfRounds,
        /** @var Option<Count> $numberOfSupplyCentersToWin */
        public Option $numberOfSupplyCentersToWin
    ) {

    }
}
