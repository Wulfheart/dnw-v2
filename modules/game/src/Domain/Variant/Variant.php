<?php

namespace Dnw\Game\Domain\Variant;

use Dnw\Game\Domain\Game\ValueObject\Count;
use Dnw\Game\Domain\Variant\Collection\VariantPowerCollection;
use Dnw\Game\Domain\Variant\Shared\VariantKey;
use Dnw\Game\Domain\Variant\ValueObject\VariantDescription;
use Dnw\Game\Domain\Variant\ValueObject\VariantName;

/**
 * @codeCoverageIgnore
 */
class Variant
{
    public function __construct(
        public VariantKey $key,
        public VariantName $name,
        public VariantDescription $description,
        public VariantPowerCollection $variantPowerCollection,
        public Count $defaultSupplyCentersToWinCount,
        public Count $totalSupplyCentersCount,
    ) {}
}
