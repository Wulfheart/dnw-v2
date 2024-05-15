<?php

namespace Dnw\Game\Core\Domain\Game\Collection;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Core\Domain\Game\ValueObject\Order\Order;

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
        $orderCollection = new self();
        foreach ($orders as $order) {
            $orderCollection->push(new Order($order));
        }

        return $orderCollection;
    }

    /**
     * @return array<string>
     */
    public function toStringArray(): array
    {
        return $this->map(fn (Order $order): string => (string) $order)->toArray();
    }
}
