<?php

namespace Dnw\Game\Core\Domain\Collection;

use Dnw\Game\Core\Domain\ValueObject\Order\Order;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int, Order>
 */
class OrderCollection implements IteratorAggregate
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
}
