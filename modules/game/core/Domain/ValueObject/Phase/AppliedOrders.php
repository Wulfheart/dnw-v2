<?php

namespace Dnw\Game\Core\Domain\ValueObject\Phase;

use Dnw\Game\Core\Domain\Collection\OrderCollection;
use Dnw\Game\Core\Domain\ValueObject\Power\PowerId;

class AppliedOrders
{
    public function __construct(
        public PowerId $powerId,
        public OrderCollection $orders,
    ) {
    }
}
