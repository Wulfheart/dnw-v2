<?php

namespace Entity;

use Collection\VariantPowerCollection;
use ValueObjects\Count;
use ValueObjects\Variant\VariantId;
use ValueObjects\Variant\VariantName;

final class Variant
{
    public function __construct(
        public VariantId $id,
        public VariantName $name,
        public VariantPowerCollection $powers,
        public Count $defaultSupplyCentersToWinCount,
        public Count $totalSupplyCentersCount,

    ) {

    }
}
