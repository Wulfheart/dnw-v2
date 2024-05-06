<?php

namespace Dnw\Foundation\Event;

interface EventDispatcherInterface
{
    public function dispatch(object $event): void;
}
