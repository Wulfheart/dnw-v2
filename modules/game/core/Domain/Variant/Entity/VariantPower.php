<?php

namespace Dnw\Game\Core\Domain\Variant\Entity;

use Dnw\Game\Core\Domain\Game\ValueObject\Color;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;
use Dnw\Game\Core\Domain\Variant\ValueObject\VariantPower\VariantPowerApiName;
use Dnw\Game\Core\Domain\Variant\ValueObject\VariantPower\VariantPowerName;

class VariantPower
{
    public function __construct(
        public VariantPowerId $id,
        public VariantPowerName $name,
        public VariantPowerApiName $apiName,
        public Color $color,
    ) {
    }
}
