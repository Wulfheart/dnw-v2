<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Variant;

use Dnw\Game\Core\Domain\Entity\Variant;
use Dnw\Game\Core\Domain\Exception\NotFoundException;
use Dnw\Game\Core\Domain\Repository\VariantRepositoryInterface;
use Dnw\Game\Core\Domain\ValueObject\Variant\VariantId;

class InMemoryVariantRepository implements VariantRepositoryInterface
{
    public function __construct(
        /** @var array<string, Variant> $variants */
        private array $variants = []
    ) {
    }

    public function load(VariantId $variantId): Variant
    {
        return $this->variants[(string) $variantId] ?? throw new NotFoundException();
    }
}
