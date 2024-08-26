<?php

namespace Dnw\Foundation\Aggregate;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AggregateVersion::class)]
class AggregateVersionTest extends TestCase
{
    public function test(): void
    {
        $version = AggregateVersion::initial();
        $this->assertTrue($version->isInitial());
        $this->assertFalse($version->isGreaterThan(AggregateVersion::initial()));
        $this->assertFalse($version->isLessThan(AggregateVersion::initial()));
        $this->assertSame(0, $version->int());

        $version = $version->increment();
        $this->assertFalse($version->isInitial());
        $this->assertTrue($version->isGreaterThan(AggregateVersion::initial()));
        $this->assertTrue($version->isLessThan(AggregateVersion::initial()->increment()->increment()));
        $this->assertSame(1, $version->int());
    }
}
