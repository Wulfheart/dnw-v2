<?php

namespace Dnw\Game\Core\Domain\Variant\Collection;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Core\Domain\Variant\Entity\VariantPower;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;
use Dnw\Game\Core\Domain\Variant\ValueObject\VariantPower\VariantPowerApiName;

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
