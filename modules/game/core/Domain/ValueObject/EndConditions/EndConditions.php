<?php

namespace Dnw\Game\Core\Domain\ValueObject\EndConditions;

use PhpOption\Option;

class EndConditions
{
    public function __construct(
        /** @var Option<MaximumNumberOfRounds> $maximumNumberOfRounds */
        public Option $maximumNumberOfRounds,
        /** @var Option<SupplyCenterCount> $numberOfSupplyCentersToWin */
        public Option $numberOfSupplyCentersToWin
    ) {

    }
}
