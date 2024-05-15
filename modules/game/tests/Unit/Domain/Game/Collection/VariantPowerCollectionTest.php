<?php

namespace Dnw\Game\Tests\Unit\Domain\Game\Collection;

use Dnw\Game\Core\Domain\Game\Collection\VariantPowerCollection;
use Dnw\Game\Tests\Mother\VariantMother;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(VariantPowerCollection::class)]
class VariantPowerCollectionTest extends TestCase
{
    public function test_getByPowerApiName(): void
    {
        $collection = VariantMother::standard()->variantPowerCollection;

        $power = $collection->getByPowerApiName($collection->getOffset(0)->powerApiName);

        $this->assertEquals($collection->getOffset(0), $power);
    }

    public function test_getByVariantPowerId(): void
    {
        $collection = VariantMother::standard()->variantPowerCollection;

        $power = $collection->getByVariantPowerId($collection->getOffset(1)->id);

        $this->assertEquals($collection->getOffset(1), $power);
    }
}
