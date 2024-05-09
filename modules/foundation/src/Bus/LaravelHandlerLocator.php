<?php

namespace Dnw\Foundation\Bus;

use League\Tactician\Handler\Locator\HandlerLocator;

final class LaravelHandlerLocator implements HandlerLocator
{
    /**
     * @inheritDoc
     */
    public function getHandlerForCommand($commandName)
    {
        $commandHandler = $commandName . 'Handler';
        return app($commandHandler);
    }
}
