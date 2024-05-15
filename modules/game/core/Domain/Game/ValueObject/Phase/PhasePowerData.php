<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject\Phase;

use Dnw\Game\Core\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use PhpOption\Option;

class PhasePowerData
{
    public function __construct(
        public bool $ordersNeeded,
        public bool $markedAsReady,
        public bool $isWinner,
        public Count $supplyCenterCount,
        public Count $unitCount,
        /** @var Option<OrderCollection> $orderCollection */
        public Option $orderCollection,
        /** @var Option<OrderCollection> $appliedOrdersCollection */
        public Option $appliedOrdersCollection,
    ) {

    }

    public function specifyAppliedOrders(OrderCollection $appliedOrdersCollection): void
    {

    }
}
