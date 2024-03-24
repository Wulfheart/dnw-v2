<?php

interface EventDispatcherInterface
{
    public function dispatch(object $event): void;
}
