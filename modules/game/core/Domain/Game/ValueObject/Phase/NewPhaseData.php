<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject\Phase;

use Dnw\Game\Core\Domain\Game\ValueObject\Count;

class NewPhaseData
{
    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        public bool $ordersNeeded,
        public bool $isWinner,
        public Count $supplyCenterCount,
        public Count $unitCount,
    ) {

    }
}
