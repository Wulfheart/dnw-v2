<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Variant;

use Dnw\Game\Core\Domain\Variant\Repository\AbstractVariantRepositoryTestCase;
use Dnw\Game\Core\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Core\Infrastructure\Repository\Phase\InMemoryPhaseRepository;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InMemoryPhaseRepository::class)]
class InMemoryPhaseRepositoryTest extends AbstractVariantRepositoryTestCase
{
    public function buildRepository(): VariantRepositoryInterface
    {
        return new InMemoryVariantRepository();
    }
}
