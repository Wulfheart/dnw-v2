<?php

namespace Dnw\Game\Tests\Factory;

use Dnw\Game\Core\Domain\Game\ValueObject\Color;
use Dnw\Game\Core\Domain\Game\ValueObject\Count;
use Dnw\Game\Core\Domain\Variant\Collection\VariantPowerCollection;
use Dnw\Game\Core\Domain\Variant\Entity\VariantPower;
use Dnw\Game\Core\Domain\Variant\Shared\VariantId;
use Dnw\Game\Core\Domain\Variant\Shared\VariantPowerId;
use Dnw\Game\Core\Domain\Variant\ValueObject\VariantApiName;
use Dnw\Game\Core\Domain\Variant\ValueObject\VariantName;
use Dnw\Game\Core\Domain\Variant\ValueObject\VariantPower\VariantPowerApiName;
use Dnw\Game\Core\Domain\Variant\ValueObject\VariantPower\VariantPowerName;
use Dnw\Game\Core\Domain\Variant\Variant;

class VariantFactory
{
    public static function standard(): Variant
    {
        $variantPowerCollection = new VariantPowerCollection([
            new VariantPower(
                VariantPowerId::new(),
                VariantPowerName::fromString('Austria'),
                VariantPowerApiName::fromString('AUSTRIA'),
                Color::fromString('red')
            ),
            new VariantPower(
                VariantPowerId::new(),
                VariantPowerName::fromString('Great Britain'),
                VariantPowerApiName::fromString('ENGLAND'),
                Color::fromString('pink')
            ),
            new VariantPower(
                VariantPowerId::new(),
                VariantPowerName::fromString('Germany'),
                VariantPowerApiName::fromString('GERMANY'),
                Color::fromString('brown')
            ),
            new VariantPower(
                VariantPowerId::new(),
                VariantPowerName::fromString('Russia'),
                VariantPowerApiName::fromString('RUSSIA'),
                Color::fromString('violet')
            ),
            new VariantPower(
                VariantPowerId::new(),
                VariantPowerName::fromString('Italy'),
                VariantPowerApiName::fromString('ITALY'),
                Color::fromString('green')
            ),
            new VariantPower(
                VariantPowerId::new(),
                VariantPowerName::fromString('France'),
                VariantPowerApiName::fromString('FRANCE'),
                Color::fromString('blue')
            ),
            new VariantPower(
                VariantPowerId::new(),
                VariantPowerName::fromString('Turkey'),
                VariantPowerApiName::fromString('TURKEY'),
                Color::fromString('yellow')
            ),
        ]);

        return new Variant(
            VariantId::new(),
            VariantName::fromString('Standard'),
            VariantApiName::fromString('standard'),
            $variantPowerCollection,
            Count::fromInt(18),
            Count::fromInt(36),
        );
    }
}
