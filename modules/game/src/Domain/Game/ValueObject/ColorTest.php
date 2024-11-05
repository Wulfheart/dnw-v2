<?php

namespace Dnw\Game\Domain\Game\ValueObject;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Color::class)]
class ColorTest extends TestCase
{
    public function test_from_string(): void
    {
        $color = Color::fromString('red');

        $this->assertEquals('red', $color->__toString());
    }

    public function test_from_empty_string(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Color cannot be empty');

        Color::fromString('');
    }
}
