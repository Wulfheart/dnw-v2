<?php

namespace Dnw\Game\Domain\Game\Collection;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Domain\Game\ValueObject\Order\Order;

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

    public function hasSameContents(OrderCollection $orderCollection): bool
    {
        $intersected = array_intersect($this->toStringArray(), $orderCollection->toStringArray());

        return count($intersected) === count($this->toStringArray());
    }
}
