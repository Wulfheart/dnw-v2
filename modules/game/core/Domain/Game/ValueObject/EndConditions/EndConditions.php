<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject\EndConditions;

use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Wulfeart\Option\Option;

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
