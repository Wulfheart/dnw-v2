<?php

namespace Dnw\Game\Tests\Unit\Domain\Game\ValueObject;

use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Count::class)]
class CountTest extends TestCase
{
    public function test_positive_case(): void
    {
        $count = Count::fromInt(1);
        $this->assertEquals(1, $count->int());

        $count = Count::zero();
        $this->assertEquals(0, $count->int());
    }

    public function test_negative_case(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Count::fromInt(-1);
    }
}
