<?php

namespace Dnw\Foundation\Collection;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ArrayCollection::class)]
class ArrayCollectionTest extends TestCase
{
    public function test_fromArray(): void
    {
        $data = [1, 2, 3];
        $collection = ArrayCollection::fromArray($data);

        $this->assertEquals($data, $collection->toArray());
    }
}
