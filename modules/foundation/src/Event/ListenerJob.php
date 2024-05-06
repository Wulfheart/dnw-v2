<?php

namespace Dnw\Foundation\Event;

use Illuminate\Foundation\Application;

final class ListenerJob
{
    public function __construct(
        private ListenerInfo $listenerInfo,
        private object $payload
    )
    {

    }

    public function displayName(): string
    {
        return $this->listenerInfo->class;
    }

    public function handle(Application $application): void
    {
        $listener = $application->make($this->listenerInfo->class);
        $listener->{$this->listenerInfo->method}($this->payload);
    }
}
