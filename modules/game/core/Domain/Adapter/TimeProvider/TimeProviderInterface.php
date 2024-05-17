<?php

namespace Dnw\Game\Core\Domain\Adapter\TimeProvider;

use Carbon\CarbonImmutable;

interface TimeProviderInterface
{
    public function getCurrentTime(): CarbonImmutable;
}
