<?php

namespace Dnw\Game\Domain\Adapter\TimeProvider;

use Dnw\Foundation\DateTime\DateTime;

class LaravelTimeProvider implements TimeProviderInterface
{
    public function getCurrentTime(): DateTime
    {
        return DateTime::now();
    }
}
