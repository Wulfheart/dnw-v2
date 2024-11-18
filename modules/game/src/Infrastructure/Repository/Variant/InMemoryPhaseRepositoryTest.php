<?php

namespace Dnw\Game\Infrastructure\Repository\Variant;

use Dnw\Game\Domain\Game\Repository\Phase\InMemoryPhaseRepository;
use Dnw\Game\Domain\Variant\Repository\AbstractVariantRepositoryTestCase;
use Dnw\Game\Domain\Variant\Repository\VariantRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InMemoryPhaseRepository::class)]
class InMemoryPhaseRepositoryTest extends AbstractVariantRepositoryTestCase
{
    public function buildRepository(): VariantRepositoryInterface
    {
        return new InMemoryVariantRepository();
    }
}
