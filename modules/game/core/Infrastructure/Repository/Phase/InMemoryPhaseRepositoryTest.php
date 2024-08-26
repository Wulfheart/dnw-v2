<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Phase;

use Dnw\Game\Core\Domain\Game\Repository\Phase\AbstractPhaseRepositoryTestCase;
use Dnw\Game\Core\Domain\Game\Repository\Phase\PhaseRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InMemoryPhaseRepository::class)]
class InMemoryPhaseRepositoryTest extends AbstractPhaseRepositoryTestCase
{
    protected function buildRepository(): PhaseRepositoryInterface
    {
        return new InMemoryPhaseRepository();
    }
}
