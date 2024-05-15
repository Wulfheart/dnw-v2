<?php

namespace Dnw\Game\Tests\Unit\Domain\Game\ValueObject\Order;

use Dnw\Game\Core\Domain\Game\ValueObject\Order\Order;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Order::class)]
class OrderTest extends TestCase
{
    public function test()
    {
        $order = Order::fromString('test');
        $this->assertEquals('test', (string) $order);
    }
}
