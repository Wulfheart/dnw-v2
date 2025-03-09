<?php

namespace Dnw\Game\Domain\Variant\Entity;

use Dnw\Game\Domain\Game\ValueObject\Color;
use Dnw\Game\Domain\Variant\Shared\VariantPowerId;
use Dnw\Game\Domain\Variant\ValueObject\VariantPower\VariantPowerName;

/**
 * @codeCoverageIgnore
 */
class VariantPower
{
    public function __construct(
        public VariantPowerId $id,
        public VariantPowerName $name,
        public Color $color,
    ) {}
}
