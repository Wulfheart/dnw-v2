<?php

declare(strict_types=1);

namespace Wulfheart\Option;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Option::class)]
final class OptionTest extends TestCase
{
    public function test_isNone(): void
    {
        $this->assertTrue(Option::none()->isNone());
        $this->assertFalse(Option::some(1)->isNone());
    }

    public function test_isSome(): void
    {
        $this->assertTrue(Option::some(1)->isSome());
        $this->assertFalse(Option::none()->isSome());
    }

    public function test_unwrap(): void
    {
        $this->assertEquals(1, Option::some(1)->unwrap());
        $this->expectException(OptionUnwrapException::class);
        Option::none()->unwrap();
    }

    public function test_unwrapOr(): void
    {
        $this->assertEquals(1, Option::some(1)->unwrapOr(2));
        $this->assertEquals(2, Option::none()->unwrapOr(2));
    }

    public function test_equals(): void
    {
        $comparator = fn (int $a, int $b): bool => $a === $b;
        $this->assertTrue(Option::some(1)->equals(Option::some(1), $comparator));
        $this->assertFalse(Option::some(1)->equals(Option::some(2), $comparator));
        $this->assertFalse(Option::some(1)->equals(Option::none(), $comparator));
        $this->assertFalse(Option::none()->equals(Option::some(1), $comparator));
        $this->assertTrue(Option::none()->equals(Option::none(), $comparator));
    }

    public function test_map(): void
    {
        $this->assertEquals(2, Option::some(1)->map(fn (int $a): int => $a + 1));

        $this->expectException(OptionUnwrapException::class);
        Option::none()->map(fn (int $a): int => $a + 1);
    }

    public function test_mapIntoOption(): void
    {
        $this->assertEquals(Option::some(2), Option::some(1)->mapIntoOption(fn (int $a): int => $a + 1));
        $this->assertEquals(Option::none(), Option::none()->mapIntoOption(fn (int $a): int => $a + 1));
    }

    public function test_mapOr(): void
    {
        $this->assertEquals(2, Option::some(1)->mapOr(fn (int $a): int => $a + 1, 3));
        $this->assertEquals(3, Option::none()->mapOr(fn (int $a): int => $a + 1, 3));
    }
}
