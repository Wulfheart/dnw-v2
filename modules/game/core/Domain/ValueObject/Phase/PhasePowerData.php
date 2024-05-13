<?php

namespace Dnw\Game\Core\Domain\ValueObject\Phase;

use Dnw\Game\Core\Domain\Collection\OrderCollection;
use Dnw\Game\Core\Domain\ValueObject\Count;
use Dnw\Game\Core\Domain\ValueObject\Power\PowerId;
use PhpOption\Option;

class PhasePowerData
{
    public function __construct(
        public PowerId $powerId,
        public bool $ordersNeeded,
        public Count $supplyCenterCount,
        public Count $unitCount,
        /** @var Option<OrderCollection> $orderCollection */
        public Option $orderCollection,
    ) {

    }
}
