<?php

namespace Dnw\Game\Core\Domain\Variant\ValueObject\VariantPower;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(VariantPowerApiName::class)]
class VariantPowerApiNameTest extends TestCase
{
    public function test_from_string(): void
    {
        $apiName = 'apiName';
        $variantPowerApiName = VariantPowerApiName::fromString($apiName);
        $this->assertEquals($apiName, $variantPowerApiName->__toString());
    }

    public function test_from_string_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        VariantPowerApiName::fromString('');
    }
}
