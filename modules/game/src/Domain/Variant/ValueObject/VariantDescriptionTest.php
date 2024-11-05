<?php

namespace Dnw\Game\Domain\Variant\ValueObject;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(VariantDescription::class)]
class VariantDescriptionTest extends TestCase
{
    public function test_from_string(): void
    {
        $description = 'description';
        $variantDescription = VariantDescription::fromString($description);
        $this->assertEquals($description, $variantDescription->__toString());
    }

    public function test_from_string_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        VariantDescription::fromString('');
    }
}
