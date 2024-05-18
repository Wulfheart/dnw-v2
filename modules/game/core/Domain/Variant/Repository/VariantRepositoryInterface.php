<?php

namespace Dnw\Game\Core\Domain\Variant\Repository;

use Dnw\Foundation\Exception\NotFoundException;
use Dnw\Game\Core\Domain\Variant\Shared\VariantId;
use Dnw\Game\Core\Domain\Variant\Variant;

interface VariantRepositoryInterface
{
    /**
     * @throws NotFoundException
     */
    public function load(VariantId $variantId): Variant;

    public function save(Variant $variant): void;
}
