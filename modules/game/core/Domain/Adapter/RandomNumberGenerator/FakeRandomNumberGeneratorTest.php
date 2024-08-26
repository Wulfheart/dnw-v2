<?php

namespace Dnw\Game\Core\Domain\Adapter\RandomNumberGenerator;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FakeRandomNumberGenerator::class)]
class FakeRandomNumberGeneratorTest extends TestCase
{
    public function test_generate(): void
    {
        $fakeRandomNumberGenerator = new FakeRandomNumberGenerator(4);
        $this->assertGreaterThanOrEqual(4, $fakeRandomNumberGenerator->generate(1, 100));
        $this->assertGreaterThanOrEqual(4, $fakeRandomNumberGenerator->generate(50, 100));
    }
}
