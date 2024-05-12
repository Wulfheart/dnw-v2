<?php

namespace Dnw\Game\Core\Domain\Entity;

use Dnw\Game\Core\Domain\Collection\VariantPowerCollection;
use Dnw\Game\Core\Domain\ValueObject\Count;
use Dnw\Game\Core\Domain\ValueObject\Variant\VariantId;
use Dnw\Game\Core\Domain\ValueObject\Variant\VariantName;

class Variant
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
