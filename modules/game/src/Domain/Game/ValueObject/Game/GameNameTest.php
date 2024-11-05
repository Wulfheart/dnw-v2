<?php

namespace Dnw\Game\Domain\Game\ValueObject\Game;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GameName::class)]
class GameNameTest extends TestCase
{
    public function test_needs_at_least_3_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        GameName::fromString('ab');
    }

    public function test_has_at_most_50_characters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        GameName::fromString(str_repeat('a', 51));
    }

    public function test_3_characters_is_valid(): void
    {
        $gameName = GameName::fromString('abc');
        $this->assertEquals('abc', (string) $gameName);
    }

    public function test_50_characters_is_valid(): void
    {
        $gameName = GameName::fromString(str_repeat('a', 50));
        $this->assertEquals(str_repeat('a', 50), (string) $gameName);
    }
}
