<?php

namespace Dnw\Game\Core\Domain\Game\Collection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(OrderCollection::class)]
class OrderCollectionTest extends TestCase
{
    public function test_from_and_to_string_array(): void
    {
        $orders = ['order1', 'order2', 'order3'];

        $orderCollection = OrderCollection::fromStringArray($orders);

        $this->assertEquals($orders, $orderCollection->toStringArray());
    }
}
