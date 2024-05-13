<?php

namespace Dnw\Game\Core\Domain\Collection;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Core\Domain\ValueObject\Phase\PhasePowerData;
use Dnw\Game\Core\Domain\ValueObject\Power\PowerId;
use PhpOption\Some;

/**
 * @extends Collection<PhasePowerData>
 */
class PhasePowerCollection extends Collection
{
    public function setOrdersForPower(PowerId $powerId, OrderCollection $orders, bool $markedAsReady): void
    {
        $phasePowerData = $this->findBy(
            fn (PhasePowerData $phasePowerData) => $phasePowerData->powerId === $powerId
        )->get();

        $phasePowerData->orderCollection = Some::create($orders);
        $phasePowerData->markedAsReady = $markedAsReady;
    }

    public function needsOrdersFromPower(PowerId $powerId): bool
    {
        $phasePowerData = $this->getByPowerId($powerId);

        return $phasePowerData->ordersNeeded;

    }

    public function markOrderStatus(PowerId $powerId, bool $orderStatus): void
    {
        $phasePowerData = $this->getByPowerId($powerId);

        $phasePowerData->markedAsReady = $orderStatus;
    }

    public function ordersMarkedAsReadyFromPower(PowerId $powerId): bool
    {
        $phasePowerData = $this->getByPowerId($powerId);

        return $phasePowerData->markedAsReady;
    }

    private function getByPowerId(PowerId $powerId): PhasePowerData
    {
        return $this->findBy(
            fn (PhasePowerData $phasePowerData) => $phasePowerData->powerId === $powerId
        )->get();
    }
}
