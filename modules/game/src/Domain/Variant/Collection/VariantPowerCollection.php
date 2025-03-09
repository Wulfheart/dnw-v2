<?php

namespace Dnw\Game\Domain\Variant\Collection;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Domain\Variant\Entity\VariantPower;
use Dnw\Game\Domain\Variant\Shared\VariantPowerKey;

/**
 * @extends Collection<VariantPower>
 */
class VariantPowerCollection extends Collection
{
    public function getByVariantPowerId(VariantPowerKey $variantPowerId): VariantPower
    {
        return $this->findBy(
            fn (VariantPower $variantPower) => $variantPower->key == $variantPowerId
        )->unwrap();
    }
}
