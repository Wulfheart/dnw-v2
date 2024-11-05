<?php

namespace Dnw\Game\Domain\Game\Test\Factory;

use Dnw\Game\Domain\Game\Collection\VariantPowerIdCollection;
use Dnw\Game\Domain\Game\ValueObject\Count;
use Dnw\Game\Domain\Game\ValueObject\Variant\GameVariantData;
use Dnw\Game\Domain\Variant\Shared\VariantId;
use Dnw\Game\Domain\Variant\Shared\VariantPowerId;
use Dnw\Game\Domain\Variant\Variant;

/**
 * @codeCoverageIgnore
 */
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

    public static function fromVariant(Variant $variant): GameVariantData
    {
        return new GameVariantData(
            $variant->id,
            VariantPowerIdCollection::fromCollection(
                $variant->variantPowerCollection->map(fn ($variantPower) => $variantPower->id)
            ),
            $variant->defaultSupplyCentersToWinCount
        );
    }
}
