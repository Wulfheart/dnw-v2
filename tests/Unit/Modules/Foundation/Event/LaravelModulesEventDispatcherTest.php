<?php

namespace Tests\Unit\Modules\Foundation\Event;

use Dnw\Foundation\Event\LaravelModulesEventDispatcher;
use Dnw\Modules\ModuleOne\Events\EventOne;
use Dnw\Modules\ModuleOne\Listener\ListenerOne;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase as FrameworkTestCase;

#[CoversClass(LaravelModulesEventDispatcher::class)]
class LaravelModulesEventDispatcherTest extends FrameworkTestCase
{
    public function test_does_not_dispatch_to_queue_if_event_and_listener_in_same_domain(): void
    {
        Event::listen(
            EventOne::class, [ListenerOne::class]
        );
        Queue::fake();

        $event = new EventOne();

        Event::assertDispatched();
    }
    public function test_does_dispatches_to_queue_if_event_and_listener_in_same_domain(): void
    {

    }
}

namespace Dnw\Modules\ModuleOne\Events;

class EventOne
{
}

namespace Dnw\Modules\ModuleOne\Listener;

use Dnw\Modules\ModuleOne\Events\EventOne;

class ListenerOne
{
    public function handle(EventOne $event): void
    {

    }
}

namespace Dnw\Modules\ModuleTwo\Listener;

use Dnw\Modules\ModuleOne\Events\EventOne;

class ListenerTwo
{
    public function handle(EventOne $event): void
    {

    }
}
