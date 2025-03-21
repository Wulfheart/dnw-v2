<?php

namespace Dnw\Game\Domain\Game\Repository\Phase\Impl\InMemory;

use Dnw\Game\Domain\Game\Repository\Phase\AbstractPhaseRepositoryTestCase;
use Dnw\Game\Domain\Game\Repository\Phase\PhaseRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InMemoryPhaseRepository::class)]
class InMemoryPhaseRepositoryTest extends AbstractPhaseRepositoryTestCase
{
    protected function buildRepository(): PhaseRepositoryInterface
    {
        return new InMemoryPhaseRepository();
    }
}
