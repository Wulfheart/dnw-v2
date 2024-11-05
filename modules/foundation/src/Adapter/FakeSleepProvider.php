<?php

namespace Dnw\Foundation\Adapter;

use PHPUnit\Framework\Assert;

final class FakeSleepProvider implements SleepProviderInterface
{
    /** @var array<int> */
    private array $sleeps = [];

    private int $i = 0;

    public function __construct(
        int ...$sleeps
    ) {
        $this->sleeps = $sleeps;
    }

    public function sleep(int $milliseconds): void
    {
        Assert::assertLessThanOrEqual(count($this->sleeps), $this->i, 'Too many sleeps submitted');

        Assert::assertEquals($this->sleeps[$this->i], $milliseconds, 'Sleep time does not match');

        $this->i++;
    }
}
