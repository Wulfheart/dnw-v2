<?php

namespace Dnw\Game\Core\Domain\Game\Repository;

use Dnw\Foundation\Exception\NotFoundException;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\GameVariantData;
use Dnw\Game\Core\Domain\Variant\Shared\VariantId;

interface VariantRepositoryInterface
{
    /**
     * @throws NotFoundException
     */
    public function load(VariantId $variantId): GameVariantData;
}
