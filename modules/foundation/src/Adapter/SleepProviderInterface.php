<?php

namespace Dnw\Foundation\Adapter;

interface SleepProviderInterface
{
    public function sleep(int $milliseconds): void;
}
