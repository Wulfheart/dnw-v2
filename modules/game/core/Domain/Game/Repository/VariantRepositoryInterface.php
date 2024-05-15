<?php

namespace Dnw\Game\Core\Domain\Game\Repository;

use Dnw\Game\Core\Domain\Game\Entity\Variant;
use Dnw\Game\Core\Domain\Game\Exception\NotFoundException;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantId;

interface VariantRepositoryInterface
{
    /**
     * @throws NotFoundException
     */
    public function load(VariantId $variantId): Variant;
}
