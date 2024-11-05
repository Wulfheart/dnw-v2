<?php

namespace Dnw\Game\Domain\Game\ValueObject\EndConditions;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MaximumNumberOfRounds::class)]
class MaximumNumberOfRoundsTest extends TestCase
{
    public function test_cannot_create_maximum_number_of_rounds_lower_than_four(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new MaximumNumberOfRounds(0);
    }

    public function test_can_create_minimum_number_of_rounds_four(): void
    {
        $maximumNumberOfRounds = new MaximumNumberOfRounds(4);
        $this->assertEquals(4, $maximumNumberOfRounds->rounds());
    }

    public function test_can_create_maximum_number_of_rounds(): void
    {
        $maximumNumberOfRounds = new MaximumNumberOfRounds(200);
        $this->assertEquals(200, $maximumNumberOfRounds->rounds());
    }

    public function test_cannot_create_maximum_number_of_rounds_greater_than_200(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new MaximumNumberOfRounds(201);
    }
}
