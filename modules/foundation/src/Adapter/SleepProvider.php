<?php

namespace Dnw\Foundation\Adapter;

use InvalidArgumentException;

/**
 * @codeCoverageIgnore
 */
class SleepProvider implements SleepProviderInterface
{
    public function sleep(int $milliseconds): void
    {
        if ($milliseconds < 0) {
            throw new InvalidArgumentException('Sleep time must be a positive integer');
        }
        usleep($milliseconds * 1000);
    }
}
