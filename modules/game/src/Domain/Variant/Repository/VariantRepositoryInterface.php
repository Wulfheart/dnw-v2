<?php

namespace Dnw\Game\Domain\Variant\Repository;

use Dnw\Game\Domain\Variant\Shared\VariantKey;
use Dnw\Game\Domain\Variant\Variant;

interface VariantRepositoryInterface
{
    public function load(VariantKey $variantKey): LoadVariantResult;

    public function keyExists(VariantKey $variantKey): bool;

    public function save(Variant $variant): SaveVariantResult;
}
