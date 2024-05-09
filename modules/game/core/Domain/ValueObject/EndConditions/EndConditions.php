<?php

namespace ValueObjects\EndConditions;

use PhpOption\Option;

final class EndConditions
{
    public function __construct(
        /** @var Option<MaximumNumberOfRounds> $maximumNumberOfRounds */
        public Option $maximumNumberOfRounds,
        /** @var Option<SupplyCenterCount> $numberOfSupplyCentersToWin */
        public Option $numberOfSupplyCentersToWin
    ) {

    }
}
