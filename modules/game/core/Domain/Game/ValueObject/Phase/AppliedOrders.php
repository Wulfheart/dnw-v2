<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject\Phase;

use Dnw\Game\Core\Domain\Game\Collection\OrderCollection;
use Dnw\Game\Core\Domain\Game\ValueObject\Power\PowerId;

class AppliedOrders
{
    public function __construct(
        public PowerId $powerId,
        public OrderCollection $orders,
    ) {
    }
}
