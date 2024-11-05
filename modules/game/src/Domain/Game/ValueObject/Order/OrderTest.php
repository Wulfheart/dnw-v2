<?php

namespace Dnw\Game\Domain\Game\ValueObject\Order;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Order::class)]
class OrderTest extends TestCase
{
    public function test(): void
    {
        $order = Order::fromString('test');
        $this->assertEquals('test', (string) $order);
    }
}
