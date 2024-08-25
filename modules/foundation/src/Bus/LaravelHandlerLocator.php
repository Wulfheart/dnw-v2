<?php

namespace Dnw\Foundation\Bus;

use Illuminate\Contracts\Foundation\Application;
use League\Tactician\Exception\MissingHandlerException;
use League\Tactician\Handler\Locator\HandlerLocator;

readonly class LaravelHandlerLocator implements HandlerLocator
{
    public function __construct(
        private Application $application,
    ) {}

    /**
     * {@inheritDoc}
     */
    public function getHandlerForCommand($commandName)
    {
        $handlerClassName = $commandName . 'Handler';

        if (class_exists($handlerClassName)) {
            return $this->application->make($handlerClassName);
        }

        $handlerInterface = $commandName . 'HandlerInterface';
        if ($this->application->has($handlerInterface)) {
            return $this->application->make($handlerInterface);
        }
        throw new MissingHandlerException("Handler not found for command $commandName");
    }
}
