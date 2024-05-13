<?php

namespace Dnw\Game\Core\Domain\Collection;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Core\Domain\ValueObject\Order\Order;
use Traversable;

/**
 * @extends Collection<Order>
 */
class OrderCollection extends Collection
{
    public function getIterator(): Traversable
    {
        // TODO: Implement getIterator() method.
    }

    /**
     * @param  array<string>  $orders
     */
    public function fromStringArray(array $orders): self
    {

    }

    /**
     * @return array<string>
     */
    public function toStringArray(): array
    {

    }
}
