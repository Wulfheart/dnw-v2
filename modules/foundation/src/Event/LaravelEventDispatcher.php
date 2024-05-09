<?php

namespace Dnw\Foundation\Event;

use Illuminate\Contracts\Queue\Queue;
use Illuminate\Foundation\Application;

readonly class LaravelEventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private Queue $queue,
        /** @var array<class-string, array<ListenerInfo>> */
        private array $events,
        private Application $application,
    ) {

    }

    public function dispatch(object $event): void
    {
        StoredEventModel::create([
            'fqdn' => get_class($event),
            'payload' => serialize($event),
            'recorded_at' => now(),
        ]);

        foreach ($this->events[$event::class] ?? [] as $listenerInfo) {
            if ($listenerInfo->isAsync) {
                $this->queue->push(new ListenerJob($listenerInfo, $event));
            } else {
                $listener = $this->application->make($listenerInfo->class);
                $listener->{$listenerInfo->method}($event);
            }
        }

    }
}
