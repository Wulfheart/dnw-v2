<?php

namespace Dnw\Foundation\Event;

interface EventDispatcherInterface
{
    public function dispatch(object $event): void;

    /**
     * @param  array<object>  $events
     */
    public function dispatchMultiple(array $events): void;
}
