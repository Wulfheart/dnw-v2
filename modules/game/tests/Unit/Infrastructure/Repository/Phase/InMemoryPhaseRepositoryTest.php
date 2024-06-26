<?php

namespace Dnw\Game\Tests\Unit\Infrastructure\Repository\Phase;

use Dnw\Game\Core\Domain\Game\Repository\PhaseRepositoryInterface;
use Dnw\Game\Core\Infrastructure\Repository\Phase\InMemoryPhaseRepository;
use Dnw\Game\Tests\Unit\Domain\Game\Repository\AbstractPhaseRepositoryTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InMemoryPhaseRepository::class)]
class InMemoryPhaseRepositoryTest extends AbstractPhaseRepositoryTestCase
{
    protected function buildRepository(): PhaseRepositoryInterface
    {
        return new InMemoryPhaseRepository();
    }
}
