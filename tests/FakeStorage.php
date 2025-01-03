<?php

namespace Tests;

use Illuminate\Support\Facades\Storage;
use Tests\Attribute\Setup;

trait FakeStorage
{
    #[Setup]
    protected function setupFakeStorage(): void
    {
        Storage::fake();
    }
}
