<?php

namespace Dnw\Game\Tests\Unit\Domain\Game\ValueObject\AdjudicationTiming;

use Dnw\Game\Core\Domain\Game\ValueObject\AdjudicationTiming\PhaseLength;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PhaseLength::class)]
class PhaseLengthTest extends TestCase
{
    public function test_minutes()
    {
        $length = PhaseLength::fromMinutes(10);
        $this->assertEquals(10, $length->minutes());
    }

    public function test_length_needs_to_be_at_least_ten()
    {
        $this->expectException(InvalidArgumentException::class);
        PhaseLength::fromMinutes(9);
    }
}
