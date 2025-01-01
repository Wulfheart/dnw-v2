<?php

namespace App\Web\Game\Helper;

use Dnw\Foundation\DateTime\DateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PhaseLengthFormatter::class)]
class PhaseLengthFormatterTest extends TestCase
{
    private const int MINUTES_PER_HOUR = 60;

    private const int HOURS_PER_DAY = 24;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_formatMinutes_de(): void
    {
        $formatter = new PhaseLengthFormatter('de');

        $this->assertEquals('1 Minute', $formatter->formatMinutes(1));
        $this->assertEquals('2 Tage', $formatter->formatMinutes(self::MINUTES_PER_HOUR * self::HOURS_PER_DAY * 2));
        $this->assertEquals('1 Tag', $formatter->formatMinutes(self::MINUTES_PER_HOUR * self::HOURS_PER_DAY));
    }

    public function test_formatMinutes_en(): void
    {
        $formatter = new PhaseLengthFormatter('en');

        $this->assertEquals('1 minute', $formatter->formatMinutes(1));
        $this->assertEquals('2 days', $formatter->formatMinutes(self::MINUTES_PER_HOUR * self::HOURS_PER_DAY * 2));
        $this->assertEquals('1 day', $formatter->formatMinutes(self::MINUTES_PER_HOUR * self::HOURS_PER_DAY));
    }

    public function test_formatDateTime_de(): void
    {
        $formatter = new PhaseLengthFormatter('de');
        $dateTime = new DateTime('1998-12-11 12:45:00');

        $this->assertEquals('Fr. 11. Dez 1998 12:45', $formatter->formatDateTime($dateTime));
    }

    public function test_formatDateTime_en(): void
    {
        $formatter = new PhaseLengthFormatter('en');
        $dateTime = new DateTime('1998-12-11 12:45:00');

        $this->assertEquals('Fri 11. Dec 1998 12:45', $formatter->formatDateTime($dateTime));
    }
}
