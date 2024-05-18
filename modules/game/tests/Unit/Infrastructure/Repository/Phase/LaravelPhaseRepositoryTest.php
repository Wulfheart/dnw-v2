<?php

namespace Dnw\Game\Tests\Unit\Infrastructure\Repository\Phase;

use Dnw\Game\Core\Domain\Game\Repository\PhaseRepositoryInterface;
use Dnw\Game\Core\Infrastructure\Repository\Phase\LaravelPhaseRepository;
use Dnw\Game\Tests\Unit\Domain\Game\Repository\AbstractPhaseRepositoryTestCase;
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
