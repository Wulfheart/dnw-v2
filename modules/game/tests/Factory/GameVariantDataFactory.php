<?php

namespace Dnw\Game\Tests\Factory;

use Dnw\Game\Core\Domain\Game\Collection\VariantPowerIdCollection;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Game\ValueObject\Variant\GameVariantData;
use Dnw\Game\Core\Domain\Variant\Shared\VariantId;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;

class GameVariantDataFactory
{
    public static function build(
        ?VariantId $variantId = null,
        ?VariantPowerIdCollection $variantPowerIdCollection = null,
        ?Count $defaultSupplyCenterCountToWin = null
    ): GameVariantData {
        return new GameVariantData(
            $variantId ?? VariantId::new(),
            $variantPowerIdCollection ?? VariantPowerIdCollection::build(
                VariantPowerId::new(),
                VariantPowerId::new(),
            ),
            $defaultSupplyCenterCountToWin ?? Count::fromInt(18)
        );
    }
}
