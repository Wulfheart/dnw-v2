<?php

namespace ValueObjects\Phase;

use ValueObjects\Power\PowerId;

final class PhasePowerData
{
    public function __construct(
        public PowerId $powerId,
        public bool $ordersNeeded,

    ) {

    }
}
