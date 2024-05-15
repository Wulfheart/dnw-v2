<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Variant;

use Dnw\Foundation\Exception\NotFoundException;
use Dnw\Game\Core\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Core\Domain\Variant\Shared\VariantId;
use Dnw\Game\Core\Domain\Variant\Variant;

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
