<?php

namespace Dnw\Game\Domain\Variant;

use Dnw\Game\Domain\Game\ValueObject\Count;
use Dnw\Game\Domain\Variant\Collection\VariantPowerCollection;
use Dnw\Game\Domain\Variant\Shared\VariantId;
use Dnw\Game\Domain\Variant\ValueObject\VariantApiName;
use Dnw\Game\Domain\Variant\ValueObject\VariantDescription;
use Dnw\Game\Domain\Variant\ValueObject\VariantName;

/**
 * @codeCoverageIgnore
 */
class Variant
{
    public function __construct(
        public VariantId $id,
        public VariantName $name,
        public VariantApiName $apiName,
        public VariantDescription $description,
        public VariantPowerCollection $variantPowerCollection,
        public Count $defaultSupplyCentersToWinCount,
        public Count $totalSupplyCentersCount,
    ) {}
}
