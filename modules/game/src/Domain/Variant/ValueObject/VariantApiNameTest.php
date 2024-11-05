<?php

namespace Dnw\Game\Domain\Variant\ValueObject;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(VariantApiName::class)]
class VariantApiNameTest extends TestCase
{
    public function test_from_string(): void
    {
        $apiName = 'apiName';
        $variantApiName = VariantApiName::fromString($apiName);
        $this->assertEquals($apiName, $variantApiName->__toString());
    }

    public function test_from_string_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        VariantApiName::fromString('');
    }
}
