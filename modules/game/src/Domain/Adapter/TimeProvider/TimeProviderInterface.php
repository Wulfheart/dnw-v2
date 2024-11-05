<?php

namespace Dnw\Game\Domain\Adapter\TimeProvider;

use Dnw\Foundation\DateTime\DateTime;

interface TimeProviderInterface
{
    public function getCurrentTime(): DateTime;
}
