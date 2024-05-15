<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject\Phase;

use Dnw\Game\Core\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Core\Domain\Game\Exception\DomainException;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Power\PowerId;
use PhpOption\Option;
use PhpOption\Some;

class PhasePowerData
{
    public function __construct(
        public PowerId $powerId,
        public bool $ordersNeeded,
        public bool $markedAsReady,
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
        if ($this->appliedOrdersCollection->isDefined()) {
            throw new DomainException('Applied orders already specified for power ' . $this->powerId . ' in phase ');
        }

        $this->appliedOrdersCollection = Some::create($appliedOrdersCollection);
    }
}
