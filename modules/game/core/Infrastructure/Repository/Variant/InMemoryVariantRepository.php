<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Variant;

use Dnw\Foundation\Exception\NotFoundException;
use Dnw\Game\Core\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Core\Domain\Variant\Shared\VariantId;
use Dnw\Game\Core\Domain\Variant\Variant;

class InMemoryVariantRepository implements VariantRepositoryInterface
{
    /** @var array<string, Variant> */
    private array $variants = [];

    /**
     * @param  array<Variant>  $variants
     */
    public function __construct(
        array $variants = []
    ) {
        foreach ($variants as $variant) {
            $this->variants[(string) $variant->id] = $variant;
        }
    }

    public function load(VariantId $variantId): Variant
    {
        return $this->variants[(string) $variantId] ?? throw new NotFoundException();
    }
}
