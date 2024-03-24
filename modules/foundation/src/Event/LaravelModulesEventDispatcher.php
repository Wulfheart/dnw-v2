<?php

namespace Dnw\Foundation\Event;

use Closure;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Str;

final class LaravelModulesEventDispatcher extends Dispatcher
{
    private const string BASE_MODULE_NAMESPACE = 'Dnw\\Modules\\';

    public function dispatch(mixed $event, $payload = [], $halt = false): ?array
    {
        if (!is_object($event)) {
            return parent::dispatch($event, $payload, $halt);
        }

        $isModuleEvent = str_starts_with($event::class, self::BASE_MODULE_NAMESPACE);

        if (!$isModuleEvent) {
            return parent::dispatch($event, $payload, $halt);
        }

        return $this->invokeModuleEvent($event, $payload, $halt);
    }

    protected function invokeModuleListeners(object $event, array $payload): ?array
    {
        $payload = $this->parseEventAndPayload($event, $payload);

        foreach ($this->getListeners($event) as $listener) {
            $listener = $this->shouldBeQueued($event, $listener)
                ? $this->createQueuedHandlerCallable($listener::class, 'handle')
                : $this->createClassListener($listener);
        }
        return null;
    }

    protected function createQueuedHandlerCallable($class, $method): Closure
    {
        // Mostly copied from the parent method
        return function () use ($class, $method) {
            $arguments = array_map(function ($a) {
                return is_object($a) ? clone $a : $a;
            }, func_get_args());

            $this->queueHandler($class, $method, $arguments);
        };
    }

    /**
     * @param class-string $event
     * @param class-string $listener
     */
    private function shouldBeQueued(string $event, string $listener): bool
    {
        if (
            str_starts_with($listener, self::BASE_MODULE_NAMESPACE)
        ) {
            $eventNamespaceParts = collect(explode('\\', Str::replaceStart(self::BASE_MODULE_NAMESPACE, '', $event)));
            $listenerNamespaceParts = collect(explode('\\', Str::replaceStart(self::BASE_MODULE_NAMESPACE, '', $event)));

            // Removing the class name to only get the namespace part
            $eventNamespaceParts->pop();
            $listenerNamespaceParts->pop();

            return $eventNamespaceParts->intersect($listenerNamespaceParts)->count() > 0;
        }

        return false;
    }
}
