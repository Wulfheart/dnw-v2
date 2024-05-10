<?php

namespace Dnw\Foundation\Event;

trait AggregateEventTrait
{
    /**
     * @var array<object>
     */
    private array $events = [];

    /**
     * @return array<object>
     */
    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    private function pushEvent(object $event): void
    {
        $this->events[] = $event;
    }
}
