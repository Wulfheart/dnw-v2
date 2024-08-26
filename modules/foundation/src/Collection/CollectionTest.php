<?php

namespace Dnw\Foundation\Collection;

use Iterator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Collection::class)]
class CollectionTest extends TestCase
{
    public function test(): void
    {
        $collection = new ArrayCollection([1, 2, 3]);

        $this->assertEquals([1, 2, 3], $collection->toArray());
        $this->assertFalse($collection->contains(fn (int $item) => $item === 4));

        $collection->push(4);
        $this->assertEquals([1, 2, 3, 4], $collection->toArray());
        $this->assertTrue($collection->contains(fn (int $item) => $item === 4));
        $this->assertEquals(4, $collection->count());
        $this->assertEquals(1, $collection->first());
        $this->assertEquals(4, $collection->getOffset(3));
        $this->assertFalse($collection->isEmpty());

        $mapped = $collection->map(fn (int $item) => $item * 2);
        $this->assertEquals([2, 4, 6, 8], $mapped->toArray());

        $filtered = $mapped->filter(fn (int $item) => $item > 4);
        $this->assertEquals([6, 8], $filtered->toArray());

        $this->assertTrue($collection->every(fn (int $item) => $item > 0));
        $this->assertFalse($collection->every(fn (int $item) => $item > 3));

        $this->assertInstanceOf(Iterator::class, $collection->getIterator());

        $this->assertEquals([1, 2, 3, 4], ArrayCollection::fromCollection($collection)->toArray());

        $collection = ArrayCollection::build(1, 2);
        $this->assertEquals([1, 2], $collection->toArray());

        $collection = ArrayCollection::empty();
        $this->assertEquals([], $collection->toArray());
    }
}
