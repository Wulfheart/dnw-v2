<?php

namespace Dnw\Game\Core\Domain\Adapter\TimeProvider;

use Carbon\CarbonImmutable;

class FakeTimeProvider implements TimeProviderInterface
{
    private CarbonImmutable $currentTime;

    public function __construct(
        string|CarbonImmutable $currentTime
    ) {
        $this->currentTime = $currentTime instanceof CarbonImmutable ? $currentTime : CarbonImmutable::parse($currentTime);
    }

    public function getCurrentTime(): CarbonImmutable
    {
        return $this->currentTime;
    }
}
