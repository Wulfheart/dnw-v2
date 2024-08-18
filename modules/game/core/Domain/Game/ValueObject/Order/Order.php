<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject\Order;

use Stringable;

class Order implements Stringable
{
    public function __construct(
        private string $order
    ) {}

    public static function fromString(string $order): self
    {
        return new self($order);
    }

    public function __toString()
    {
        return $this->order;
    }
}
