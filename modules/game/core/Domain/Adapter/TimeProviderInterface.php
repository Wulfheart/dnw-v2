<?php

namespace Dnw\Game\Core\Domain\Adapter;

use Carbon\CarbonImmutable;

interface TimeProviderInterface
{
    public function getCurrentTime(): CarbonImmutable;
}
