<?php

namespace Dnw\Game\Domain\Variant\Repository\Impl\InMemory;

use Dnw\Game\Domain\Variant\Repository\AbstractVariantRepositoryTestCase;
use Dnw\Game\Domain\Variant\Repository\VariantRepositoryInterface;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(InMemoryVariantRepository::class)]
class InMemoryVariantRepositoryTest extends AbstractVariantRepositoryTestCase
{
    public function buildRepository(): VariantRepositoryInterface
    {
        return new InMemoryVariantRepository();
    }
}
