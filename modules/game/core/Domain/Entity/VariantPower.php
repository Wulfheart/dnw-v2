<?php

namespace Dnw\Game\Core\Domain\Entity;

use Dnw\Game\Core\Domain\ValueObject\Color;
use Dnw\Game\Core\Domain\ValueObject\Count;
use Dnw\Game\Core\Domain\ValueObject\Variant\VariantPower\VariantPowerApiName;
use Dnw\Game\Core\Domain\ValueObject\Variant\VariantPower\VariantPowerId;
use Dnw\Game\Core\Domain\ValueObject\Variant\VariantPower\VariantPowerName;

class VariantPower
{
    public function __construct(
        public VariantPowerId $id,
        public VariantPowerName $name,
        public VariantPowerApiName $powerApiName,
        public Color $color,
        public Count $startingSupplyCenterCount,
    ) {
    }
}
