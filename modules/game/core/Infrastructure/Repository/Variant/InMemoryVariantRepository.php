<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Variant;

use Dnw\Foundation\Exception\NotFoundException;
use Dnw\Game\Core\Domain\Game\Repository\VariantRepositoryInterface;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\GameVariantData;
use Dnw\Game\Core\Domain\Variant\Shared\VariantId;

class InMemoryVariantRepository implements VariantRepositoryInterface
{
    public function __construct(
        /** @var array<string, GameVariantData> $variants */
        private array $variants = []
    ) {
    }

    public function load(VariantId $variantId): GameVariantData
    {
        return $this->variants[(string) $variantId] ?? throw new NotFoundException();
    }
}
