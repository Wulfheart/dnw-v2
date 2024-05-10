<?php

namespace Dnw\Game\Core\Domain\Repository;

use Dnw\Game\Core\Domain\Entity\Variant;
use Dnw\Game\Core\Domain\Exception\NotFoundException;
use Dnw\Game\Core\Domain\ValueObject\Variant\VariantId;

interface VariantRepositoryInterface
{
    /**
     * @throws NotFoundException
     */
    public function load(VariantId $variantId): Variant;
}
