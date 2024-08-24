<?php

namespace Dnw\Game\Core\Infrastructure\Repository\Variant;

use Dnw\Game\Core\Domain\Variant\Repository\LoadVariantResult;
use Dnw\Game\Core\Domain\Variant\Repository\SaveVariantResult;
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

    public function load(VariantId $variantId): LoadVariantResult
    {
        $variant = $this->variants[(string) $variantId] ?? null;
        if (isset($variant)) {
            return LoadVariantResult::ok($variant);
        }

        return LoadVariantResult::err(LoadVariantResult::E_VARIANT_NOT_FOUND);
    }

    public function save(Variant $variant): SaveVariantResult
    {
        $this->variants[(string) $variant->id] = $variant;
        return SaveVariantResult::ok();
    }
}
