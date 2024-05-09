<?php

namespace Dnw\Foundation\Bus;

use Illuminate\Contracts\Foundation\Application;
use League\Tactician\Handler\Locator\HandlerLocator;

readonly class LaravelHandlerLocator implements HandlerLocator
{
    public function __construct(
        private Application $application,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getHandlerForCommand($commandName)
    {
        $commandHandler = $commandName.'Handler';

        return $this->application->make($commandHandler);
    }
}
