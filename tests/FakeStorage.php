<?php

namespace Tests;

use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Before;

trait FakeStorage
{
    #[Before]
    protected function setupFakeStorage(): void
    {
        Storage::fake();
    }
}
