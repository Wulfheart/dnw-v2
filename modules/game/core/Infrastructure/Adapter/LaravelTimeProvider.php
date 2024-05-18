<?php

namespace Dnw\Game\Core\Infrastructure\Adapter;

use Dnw\Foundation\DateTime\DateTime;
use Dnw\Game\Core\Domain\Adapter\TimeProvider\TimeProviderInterface;

class LaravelTimeProvider implements TimeProviderInterface
{
    public function getCurrentTime(): DateTime
    {
        return DateTime::now();
    }
}
