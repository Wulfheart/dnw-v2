<?php

namespace Dnw\Game\Domain\Game\ValueObject\EndConditions;

use Dnw\Game\Domain\Game\ValueObject\Count;
use Wulfheart\Option\Option;

class EndConditions
{
    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        /** @var Option<MaximumNumberOfRounds> $maximumNumberOfRounds */
        public Option $maximumNumberOfRounds,
        /** @var Option<Count> $numberOfSupplyCentersToWin */
        public Option $numberOfSupplyCentersToWin
    ) {}
}
