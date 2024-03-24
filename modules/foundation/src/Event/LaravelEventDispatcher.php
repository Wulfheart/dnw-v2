<?php

namespace Dnw\Foundation\Event;

final class LaravelEventDispatcher implements EventDispatcherInterface
{
    public function dispatch(object $event): void
    {
        event($event);
    }
}
