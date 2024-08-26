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

    public function test_hasSameContents(): void
    {
        $orders = ['order1', 'order2', 'order3'];

        $orderCollection = OrderCollection::fromStringArray($orders);
        $orderCollection2 = OrderCollection::fromStringArray($orders);
        $orderCollection4 = OrderCollection::fromStringArray(['order1', 'order2', 'order4']);

        $this->assertTrue($orderCollection->hasSameContents($orderCollection2));
        $this->assertFalse($orderCollection->hasSameContents($orderCollection4));
    }
}
