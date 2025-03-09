<?php

namespace Dnw\Game\Domain\Variant\Collection;

use Dnw\Game\Domain\Game\Test\Factory\VariantFactory;
use Dnw\Game\Domain\Variant\Shared\VariantPowerId;
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

        $power = $collection->getByVariantPowerId(
            VariantPowerId::new($collection->getOffset(1)->id)
        );

        $this->assertEquals($collection->getOffset(1), $power);
    }
}
