<?php

namespace Dnw\Foundation\Event;

use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\Collection\Collection;
use PHPUnit\Framework\Assert;

class FakeEventDispatcher implements EventDispatcherInterface
{
    /**
     * @var Collection<object>
     */
    private Collection $events;

    public function __construct(

    ) {
        $this->events = new ArrayCollection();
    }

    public function dispatch(object $event): void
    {
        $this->events->push($event);
    }

    public function dispatchMultiple(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }

    /**
     * @param  class-string  $event
     * @param  int<0, max>  $expectedCount
     */
    public function assertDispatched(string $event, int $expectedCount = 0): void
    {
        $filteredEvents = $this->events->filter(
            fn (object $eventObject): bool => $eventObject instanceof $event
        );
        $count = $filteredEvents->count();

        Assert::assertTrue(
            $count === $expectedCount,
            "Expected to dispatch event [$event] $expectedCount times, but dispatched $count times."
        );
    }

    public function assertNothingDispatched(): void
    {
        Assert::assertTrue($this->events->isEmpty(), 'Expected no events to be dispatched, but some events were dispatched.');
    }
}
