<?php

namespace Dnw\Game\Core\Infrastructure\Adapter;

use Carbon\CarbonImmutable;
use Dnw\Game\Core\Domain\Adapter\TimeProviderInterface;

class LaravelTimeProvider implements TimeProviderInterface
{
    public function getCurrentTime(): CarbonImmutable
    {
        return CarbonImmutable::now();
    }
}
