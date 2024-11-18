<?php

namespace Dnw\Game\Domain\Game\Repository\Phase;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InMemoryPhaseRepository::class)]
class InMemoryPhaseRepositoryTest extends AbstractPhaseRepositoryTestCase
{
    protected function buildRepository(): PhaseRepositoryInterface
    {
        return new InMemoryPhaseRepository();
    }
}
