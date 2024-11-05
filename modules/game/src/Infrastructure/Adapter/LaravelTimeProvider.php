<?php

namespace Dnw\Game\Infrastructure\Adapter;

use Dnw\Foundation\DateTime\DateTime;
use Dnw\Game\Domain\Adapter\TimeProvider\TimeProviderInterface;

class LaravelTimeProvider implements TimeProviderInterface
{
    public function getCurrentTime(): DateTime
    {
        return DateTime::now();
    }
}
