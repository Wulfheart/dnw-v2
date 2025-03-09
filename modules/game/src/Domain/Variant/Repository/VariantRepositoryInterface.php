<?php

namespace Dnw\Game\Domain\Variant\Repository;

use Dnw\Game\Domain\Variant\Shared\VariantId;
use Dnw\Game\Domain\Variant\Variant;

interface VariantRepositoryInterface
{
    public function load(VariantId $variantId): LoadVariantResult;

    public function loadByName(VariantName $variantName): LoadVariantResult;

    public function save(Variant $variant): SaveVariantResult;
}
