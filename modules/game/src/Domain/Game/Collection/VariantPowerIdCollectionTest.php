<?php

namespace Dnw\Game\Domain\Game\Collection;

use Dnw\Game\Domain\Variant\Shared\VariantPowerKey;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(VariantPowerIdCollection::class)]
class VariantPowerIdCollectionTest extends TestCase
{
    public function test_containsVariantPowerId(): void
    {
        $id = VariantPowerKey::fromString('::ID::');

        $collection = new VariantPowerIdCollection([VariantPowerKey::fromString('::OTHER_ID::'), $id]);

        $this->assertTrue($collection->containsVariantPowerId($id));
        $this->assertFalse($collection->containsVariantPowerId(VariantPowerKey::fromString('::ID::')));
    }
}
