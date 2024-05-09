<?php

namespace Entity;

use ValueObjects\Color;
use ValueObjects\Count;
use ValueObjects\Variant\VariantPower\VariantPowerId;
use ValueObjects\Variant\VariantPower\VariantPowerName;

final class VariantPower
{
    public function __construct(
        public VariantPowerId $id,
        public VariantPowerName $name,
        public Color $color,
        public Count $startingSupplyCenterCount,
    ) {
    }
}
