<?php

namespace Tests;

use Illuminate\Support\Facades\Storage;
use Tests\Attribute\Setup;

/**
 * @phpstan-ignore trait.unused
 */
trait FakeStorage
{
    #[Setup]
    protected function setupFakeStorage(): void
    {
        Storage::fake();
    }
}
