<?php

namespace Dnw\Game\Core\Domain\Collection;

use Dnw\Game\Core\Domain\ValueObject\Power\PowerId;

class PhasePowerCollection
{
    public function setOrdersForPower(PowerId $powerId, OrderCollection $orders, bool $markedAsReady): void
    {

    }

    public function needsOrders(PowerId $powerId): bool
    {
        return false;
    }
}
