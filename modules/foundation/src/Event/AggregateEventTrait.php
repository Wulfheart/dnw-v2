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

    /**
     * @return array<object>
     */
    public function inspectEvents():array
    {
        return $this->events;
    }

    private function pushEvent(object $event): void
    {
        $this->events[] = $event;
    }
}
