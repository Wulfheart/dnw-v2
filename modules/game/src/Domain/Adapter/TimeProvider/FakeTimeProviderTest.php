<?php

namespace Dnw\Game\Domain\Adapter\TimeProvider;

use Dnw\Foundation\DateTime\DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FakeTimeProvider::class)]
class FakeTimeProviderTest extends TestCase
{
    public function test_from_string(): void
    {
        $provider = new FakeTimeProvider('2021-01-01 12.00.00');

        $this->assertEquals('2021-01-01 12:00:00', $provider->getCurrentTime()->toDateTimeString());
    }

    public function test_from_date_time(): void
    {
        $provider = new FakeTimeProvider(new DateTime('2021-01-01 12.00.00'));

        $this->assertEquals('2021-01-01 12:00:00', $provider->getCurrentTime()->toDateTimeString());
    }
}
