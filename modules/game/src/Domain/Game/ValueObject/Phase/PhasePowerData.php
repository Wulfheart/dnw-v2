<?php

namespace Dnw\Game\Domain\Game\ValueObject\Phase;

use Dnw\Game\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Domain\Game\ValueObject\Count;
use Wulfheart\Option\Option;

class PhasePowerData
{
    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        public bool $ordersNeeded,
        public bool $markedAsReady,
        public bool $isWinner,
        public Count $supplyCenterCount,
        public Count $unitCount,
        /** @var Option<OrderCollection> $orderCollection */
        public Option $orderCollection,
    ) {}
}
