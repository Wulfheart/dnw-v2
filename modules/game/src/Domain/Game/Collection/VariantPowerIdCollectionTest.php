<?php

namespace Dnw\Game\Domain\Game\Collection;

use Dnw\Game\Domain\Variant\Shared\VariantPowerId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(VariantPowerIdCollection::class)]
class VariantPowerIdCollectionTest extends TestCase
{
    public function test_containsVariantPowerId(): void
    {
        $id = VariantPowerId::new('::ID::');

        $collection = new VariantPowerIdCollection([VariantPowerId::new('::OTHER_ID::'), $id]);

        $this->assertTrue($collection->containsVariantPowerId($id));
        $this->assertFalse($collection->containsVariantPowerId(VariantPowerId::new('::ID::')));
    }
}
