<?php

namespace Dnw\Game\Domain\Game\Test\Factory;

use Dnw\Game\Domain\Game\Collection\VariantPowerIdCollection;
use Dnw\Game\Domain\Game\ValueObject\Variant\GameVariantData;
use Dnw\Game\Domain\Variant\Variant;

/**
 * @codeCoverageIgnore
 */
class GameVariantDataFactory
{
    public static function fromVariant(Variant $variant): GameVariantData
    {
        return new GameVariantData(
            $variant->key,
            VariantPowerIdCollection::fromCollection(
                $variant->variantPowerCollection->map(fn ($variantPower) => $variantPower->key)
            ),
            $variant->defaultSupplyCentersToWinCount
        );
    }
}
