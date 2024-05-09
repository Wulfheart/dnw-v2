<?php

namespace Dnw\Game\Core\Domain\ValueObject\Phase;

use Dnw\Game\Core\Domain\ValueObject\Power\PowerId;

class PhasePowerData
{
    public function __construct(
        public PowerId $powerId,
        public bool $ordersNeeded,

    ) {

    }
}
