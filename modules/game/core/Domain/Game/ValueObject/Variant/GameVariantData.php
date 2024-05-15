<?php

namespace Dnw\Game\Core\Domain\Game\ValueObject\Variant;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Core\Domain\Game\Collection\VariantPowerIdCollection;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Variant\Shared\VariantId;

class GameVariantData
{
    public function __construct(
        public VariantId $id,
        /** @var VariantPowerIdCollection $variantPowerIdCollection */
        public Collection $variantPowerIdCollection,
        public Count $defaultSupplyCentersToWinCount,
    ) {

    }
}
