<?php

namespace Dnw\Game\Core\Domain\Variant\Repository;

use Dnw\Foundation\Exception\NotFoundException;
use Dnw\Game\Core\Domain\Variant\Shared\VariantId;
use Dnw\Game\Core\Domain\Variant\Variant;

interface VariantRepositoryInterface
{
    public function load(VariantId $variantId): LoadVariantResult;

    public function save(Variant $variant): SaveVariantResult;
}
