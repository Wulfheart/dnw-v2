<?php

namespace Dnw\Game\Core\Domain\Adapter\TimeProvider;

use Dnw\Foundation\DateTime\DateTime;

interface TimeProviderInterface
{
    public function getCurrentTime(): DateTime;
}
