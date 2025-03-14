<?php

namespace Dnw\Game\Domain\Game\ValueObject\Variant;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Domain\Game\Collection\VariantPowerIdCollection;
use Dnw\Game\Domain\Game\ValueObject\Count;
use Dnw\Game\Domain\Variant\Shared\VariantKey;

class GameVariantData
{
    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        public VariantKey $id,
        /** @var VariantPowerIdCollection $variantPowerIdCollection */
        public Collection $variantPowerIdCollection,
        public Count $defaultSupplyCentersToWinCount,
    ) {}
}
