<?php

namespace Tests;

use Dnw\Foundation\Bus\BusInterface;

class ModuleTestCase extends LaravelTestCase
{
    protected BusInterface $bus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bus = $this->app->make(BusInterface::class);
    }

    /**
     * @template T
     *
     * @param  class-string<T>  $type
     * @return T
     */
    protected function bootstrap(string $type): mixed
    {
        return $this->app->make($type);
    }
}
