<?php

namespace Dnw\Game\Core\Domain\Game\Entity;

use Dnw\Game\Core\Domain\Game\ValueObject\Color;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantPower\VariantPowerApiName;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantPower\VariantPowerId;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantPower\VariantPowerName;

class VariantPower
{
    public function __construct(
        public VariantPowerId $id,
        public VariantPowerName $name,
        public VariantPowerApiName $powerApiName,
        public Color $color,
    ) {
    }
}
