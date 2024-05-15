<?php

namespace Dnw\Game\Core\Domain\Game\Collection;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Core\Domain\Game\Entity\VariantPower;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantPower\VariantPowerApiName;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\VariantPower\VariantPowerId;

/**
 * @extends Collection<VariantPower>
 */
class VariantPowerCollection extends Collection
{
    public function getByPowerApiName(VariantPowerApiName $powerApiName): VariantPower
    {
        return $this->findBy(
            fn (VariantPower $variantPower) => $variantPower->powerApiName === $powerApiName
        )->get();
    }

    public function getByVariantPowerId(VariantPowerId $variantPowerId): VariantPower
    {
        return $this->findBy(
            fn (VariantPower $variantPower) => $variantPower->id === $variantPowerId
        )->get();
    }
}
