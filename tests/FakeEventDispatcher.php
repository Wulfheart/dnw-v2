<?php

namespace Tests;

use Dnw\Foundation\Event\EventDispatcherInterface;
use PHPUnit\Framework\Attributes\Before;

trait FakeEventDispatcher
{
    private \Dnw\Foundation\Event\FakeEventDispatcher $fakeEventDispatcher;

    #[Before]
    protected function setupFakeEventDispatcher(): void
    {
        app()->singleton(EventDispatcherInterface::class, \Dnw\Foundation\Event\FakeEventDispatcher::class);

        /** @var \Dnw\Foundation\Event\FakeEventDispatcher $fakeEventDispatcher */
        $fakeEventDispatcher = app(EventDispatcherInterface::class);

        $this->fakeEventDispatcher = $fakeEventDispatcher;
    }

    /**
     * @param  class-string  $listener
     */
    protected function assertEventDispatched(string $listener): void
    {
        $this->fakeEventDispatcher->assertDispatched($listener);
    }
}
