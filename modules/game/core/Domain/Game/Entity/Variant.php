<?php

namespace Dnw\Game\Core\Domain\Game\Entity;

use Dnw\Game\Core\Domain\Game\Collection\VariantPowerCollection;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantApiName;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantId;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantName;

class Variant
{
    public function __construct(
        public VariantId $id,
        public VariantName $name,
        public VariantApiName $apiName,
        public VariantPowerCollection $variantPowerCollection,
        public Count $defaultSupplyCentersToWinCount,
        public Count $totalSupplyCentersCount,
    ) {

    }
}
