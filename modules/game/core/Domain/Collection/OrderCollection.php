<?php

namespace Dnw\Game\Core\Domain\Collection;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Core\Domain\ValueObject\Order\Order;

/**
 * @extends Collection<Order>
 */
class OrderCollection extends Collection
{
    /**
     * @param  array<string>  $orders
     */
    public static function fromStringArray(array $orders): self
    {

    }

    /**
     * @return array<string>
     */
    public function toStringArray(): array
    {

    }
}
