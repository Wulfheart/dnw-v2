<?php

namespace Dnw\Game\Domain\Variant\Collection;

use Dnw\Foundation\Collection\Collection;
use Dnw\Game\Domain\Variant\Entity\VariantPower;
use Dnw\Game\Domain\Variant\Shared\VariantPowerId;
use Dnw\Game\Domain\Variant\ValueObject\VariantPower\VariantPowerApiName;

/**
 * @extends Collection<VariantPower>
 */
class VariantPowerCollection extends Collection
{
    public function getByPowerApiName(VariantPowerApiName $powerApiName): VariantPower
    {
        return $this->findBy(
            fn (VariantPower $variantPower) => $variantPower->apiName == $powerApiName
        )->unwrap();
    }

    public function getByVariantPowerId(VariantPowerId $variantPowerId): VariantPower
    {
        return $this->findBy(
            fn (VariantPower $variantPower) => $variantPower->id == $variantPowerId
        )->unwrap();
    }
}
