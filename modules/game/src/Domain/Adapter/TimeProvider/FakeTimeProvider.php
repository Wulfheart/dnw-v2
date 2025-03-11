<?php

namespace Dnw\Game\Domain\Adapter\TimeProvider;

use Dnw\Foundation\DateTime\DateTime;

class FakeTimeProvider implements TimeProviderInterface
{
    private DateTime $currentTime;

    public function __construct(
        string|DateTime $currentTime
    ) {
        $this->currentTime = $currentTime instanceof DateTime ? $currentTime : new DateTime($currentTime);
    }

    public function getCurrentTime(): DateTime
    {
        return $this->currentTime;
    }

    public static function now(): self
    {
        return new self(new DateTime());
    }
}
