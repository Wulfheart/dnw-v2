<?php

namespace Tests;

use Dnw\Foundation\Event\ListenerJob;
use Illuminate\Support\Facades\Queue;
use Tests\Attribute\Setup;

trait FakeQueue
{
    #[Setup]
    protected function setupFakeQueue(): void
    {
        Queue::fake();
    }

    protected function assertListenerIsQueued(string $listener): void
    {
        Queue::assertPushed(fn (ListenerJob $job) => $job->displayName() === $listener);
    }

    protected function assertListenerIsNotQueued(string $listener): void
    {
        Queue::assertNotPushed(fn (ListenerJob $job) => $job->displayName() === $listener);
    }
}
