<?php

namespace Dnw\Game\Core\Domain\Collection;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Core\Domain\ValueObject\Phase\PhasePowerData;
use Dnw\Game\Core\Domain\ValueObject\Power\PowerId;

/**
 * @extends Collection<PhasePowerData>
 */
class PhasePowerCollection extends Collection
{
    public function setOrdersForPower(PowerId $powerId, OrderCollection $orders, bool $markedAsReady): void
    {

    }

    public function needsOrders(PowerId $powerId): bool
    {
        return false;
    }

    public function markOrderStatus(PowerId $powerId, bool $orderStatus): void
    {
    }

    public function allReadyForAdjudication(): bool
    {

    }
}
