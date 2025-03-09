<?php

namespace Dnw\Game\Domain\Variant\Entity;

use Dnw\Game\Domain\Game\ValueObject\Color;
use Dnw\Game\Domain\Variant\Shared\VariantPowerKey;
use Dnw\Game\Domain\Variant\ValueObject\VariantPower\VariantPowerName;

/**
 * @codeCoverageIgnore
 */
class VariantPower
{
    public function __construct(
        public VariantPowerKey $key,
        public VariantPowerName $name,
        public Color $color,
    ) {}
}
