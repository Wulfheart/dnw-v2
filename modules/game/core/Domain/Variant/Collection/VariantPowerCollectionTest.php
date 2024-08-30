<?php

namespace Dnw\Game\Core\Domain\Variant\Collection;

use Dnw\Game\Core\Domain\Game\Test\Factory\VariantFactory;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;
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

        $power = $collection->getByVariantPowerId(VariantPowerId::fromId($collection->getOffset(1)->id->toId()));

        $this->assertEquals($collection->getOffset(1), $power);
    }
}
