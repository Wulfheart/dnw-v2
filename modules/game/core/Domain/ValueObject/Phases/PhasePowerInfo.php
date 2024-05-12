<?php

namespace Dnw\Game\Core\Domain\ValueObject\Phases;

use Dnw\Game\Core\Domain\Collection\OrderCollection;
use Dnw\Game\Core\Domain\ValueObject\Count;
use Dnw\Game\Core\Domain\ValueObject\Power\PowerId;
use PhpOption\Option;

class PhasePowerInfo
{
    public function __construct(
        public PowerId $powerId,
        public Count $supplyCenterCount,
        public Count $unitCount,
        /** @var Option<OrderCollection> $orderCollection */
        public Option $orderCollection,
        public bool $markedAsReady,
    ) {
    }

    public function ordersNeeded(): bool
    {
        return false;
    }
}
