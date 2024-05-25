<?php

namespace Dnw\Game\Core\Domain\Variant;

use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Variant\Collection\VariantPowerCollection;
use Dnw\Game\Core\Domain\Variant\Shared\VariantId;
use Dnw\Game\Core\Domain\Variant\ValueObject\VariantApiName;
use Dnw\Game\Core\Domain\Variant\ValueObject\VariantDescription;
use Dnw\Game\Core\Domain\Variant\ValueObject\VariantName;

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
    ) {

    }
}
