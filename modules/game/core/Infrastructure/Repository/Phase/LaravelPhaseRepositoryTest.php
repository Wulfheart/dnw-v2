<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Phase;

use Dnw\Game\Core\Domain\Game\Repository\Phase\AbstractPhaseRepositoryTestCase;
use Dnw\Game\Core\Domain\Game\Repository\Phase\PhaseRepositoryInterface;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LaravelPhaseRepository::class)]
class LaravelPhaseRepositoryTest extends AbstractPhaseRepositoryTestCase
{
    protected function buildRepository(): PhaseRepositoryInterface
    {
        Storage::fake('local');
        $fake = Storage::disk('local');

        return new LaravelPhaseRepository(
            $fake
        );
    }
}
