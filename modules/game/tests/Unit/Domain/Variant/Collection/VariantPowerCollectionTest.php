<?php

namespace Dnw\Game\Tests\Unit\Domain\Variant\Collection;

use Dnw\Game\Core\Domain\Variant\Collection\VariantPowerCollection;
use Dnw\Game\Tests\Factory\VariantFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(VariantPowerCollection::class)]
class VariantPowerCollectionTest extends TestCase
{
    public function test_getByPowerApiName(): void
    {
        $collection = VariantFactory::standard()->variantPowerCollection;

        $power = $collection->getByPowerApiName($collection->getOffset(0)->apiName);

        $this->assertEquals($collection->getOffset(0), $power);
    }

    public function test_getByVariantPowerId(): void
    {
        $collection = VariantFactory::standard()->variantPowerCollection;

        $power = $collection->getByVariantPowerId($collection->getOffset(1)->id);

        $this->assertEquals($collection->getOffset(1), $power);
    }
}
