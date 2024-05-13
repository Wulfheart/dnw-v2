<?php

namespace Dnw\Foundation\Event;

use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\Collection\Collection;
use InvalidArgumentException;
use PHPUnit\Framework\Assert;
use Technically\CallableReflection\CallableReflection;
use Technically\CallableReflection\Parameters\TypeReflection;

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
     * @param  class-string|callable(object): bool  $event
     * @param  int<0, max>  $expectedCount
     */
    public function assertDispatched(string|callable $event, int $expectedCount = 0): void
    {
        if (is_string($event)) {
            $filteredEvents = $this->events->filter(
                fn (object $eventObject): bool => $eventObject instanceof $event
            );
            $count = $filteredEvents->count();
            $stringEventTypes = [$event];

        } else {
            $reflection = CallableReflection::fromCallable($event);
            $firstParameter = $reflection->getParameters()[0]
                ?? throw new InvalidArgumentException('The given callable has no parameters.');
            $eventTypes = $firstParameter->getTypes();

            $stringEventTypes = array_map(fn (TypeReflection $type) => $type->getType(), $eventTypes);

            $filteredEvents = $this->events->filter(
                function (object $eventObject) use ($eventTypes): bool {
                    foreach ($eventTypes as $parameterType) {
                        if ($eventObject::class === $parameterType->getType()) {
                            return true;
                        }
                    }

                    return false;
                }
            );

            $count = 0;
            foreach ($filteredEvents as $potentialEventCandidate) {
                $result = $event($potentialEventCandidate);
                if ($result) {
                    $count++;
                }
            }
        }

        $events = implode(', ', $stringEventTypes);
        Assert::assertTrue(
            $count === $expectedCount,
            "Expected to dispatch events [$events] $expectedCount times, but dispatched $count times."
        );
    }
}
