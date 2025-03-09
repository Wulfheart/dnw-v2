<?php

namespace Dnw\Game\Domain\Variant\Repository\Impl\InMemory;

use Dnw\Game\Domain\Variant\Repository\LoadVariantResult;
use Dnw\Game\Domain\Variant\Repository\SaveVariantResult;
use Dnw\Game\Domain\Variant\Repository\VariantRepositoryInterface;
use Dnw\Game\Domain\Variant\Shared\VariantKey;
use Dnw\Game\Domain\Variant\Variant;

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
            $this->variants[(string) $variant->key] = $variant;
        }
    }

    public function load(VariantKey $variantKey): LoadVariantResult
    {
        $variant = $this->variants[(string) $variantKey] ?? null;
        if (isset($variant)) {
            return LoadVariantResult::ok($variant);
        }

        return LoadVariantResult::err(LoadVariantResult::E_VARIANT_NOT_FOUND);
    }

    public function save(Variant $variant): SaveVariantResult
    {
        $this->variants[(string) $variant->key] = $variant;

        return SaveVariantResult::ok();
    }

    public function keyExists(VariantKey $variantKey): bool
    {
        return isset($this->variants[(string) $variantKey]);
    }
}
