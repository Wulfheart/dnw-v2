<?php

namespace Dnw\Game\Core\Domain\Repository;

use Dnw\Game\Core\Domain\Entity\Variant;
use Dnw\Game\Core\Domain\ValueObject\Variant\VariantId;

interface VariantRepositoryInterface
{
    public function find(VariantId $variantId): Variant;
}
