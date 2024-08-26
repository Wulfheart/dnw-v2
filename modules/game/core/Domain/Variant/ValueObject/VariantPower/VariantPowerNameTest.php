<?php

namespace Dnw\Game\Core\Domain\Variant\ValueObject\VariantPower;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(VariantPowerName::class)]
class VariantPowerNameTest extends TestCase
{
    public function test_from_string(): void
    {
        $name = 'name';
        $variantPowerName = VariantPowerName::fromString($name);
        $this->assertEquals($name, $variantPowerName->__toString());
    }

    public function test_from_string_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        VariantPowerName::fromString('');
    }
}
