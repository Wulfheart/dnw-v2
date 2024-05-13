<?php

namespace Dnw\Foundation\Bus;

use Dnw\Foundation\Adapter\SleepProviderInterface;
use Dnw\Foundation\Aggregate\NewerAggregateVersionAvailableException;
use League\Tactician\Middleware;

readonly class RetryIfNewerAggregateVersionIsAvailableMiddleware implements Middleware
{
    /** @var array<int> */
    private const array BACKOFF_SEQUENCE = [10, 10, 20, 100];

    public function __construct(
        private SleepProviderInterface $sleepProvider,
    ) {

    }

    public function execute($command, callable $next)
    {
        $i = 0;

        while ($i < count(self::BACKOFF_SEQUENCE)) {
            try {
                return $next($command);
            } catch (NewerAggregateVersionAvailableException $e) {
                $this->sleepProvider->sleep(self::BACKOFF_SEQUENCE[$i]);
                $i++;
            }
        }

        return $next($command);
    }
}
