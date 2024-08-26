<?php

namespace Dnw\Game\Core\Domain\Variant\ValueObject;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(VariantName::class)]
class VariantNameTest extends TestCase
{
    public function test_from_string(): void
    {
        $name = 'name';
        $variantName = VariantName::fromString($name);
        $this->assertEquals($name, $variantName->__toString());
    }

    public function test_from_string_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        VariantName::fromString('');
    }
}
