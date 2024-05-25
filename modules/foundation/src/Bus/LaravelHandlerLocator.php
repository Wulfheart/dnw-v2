<?php

namespace Dnw\Foundation\Bus;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use League\Tactician\Exception\MissingHandlerException;
use League\Tactician\Handler\Locator\HandlerLocator;
use Psr\Log\LoggerInterface;

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
        $handlerClassName = $commandName . 'Handler';
        try {
            return $this->application->make($handlerClassName);
        } catch (BindingResolutionException $e) {
        }

        $handlerInterface = $commandName . 'HandlerInterface';
        $isPresent = $this->application->has($handlerInterface);
        if($isPresent) {
            return $this->application->make($handlerInterface);
        }
        throw new MissingHandlerException("Handler not found for command $commandName");
    }
}
