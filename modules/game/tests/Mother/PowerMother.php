<?php

namespace Dnw\Game\Tests\Mother;

use Dnw\Game\Core\Domain\Entity\Power;
use Dnw\Game\Core\Domain\ValueObject\Player\PlayerId;
use Dnw\Game\Core\Domain\ValueObject\Power\PowerId;
use Dnw\Game\Core\Domain\ValueObject\Variant\VariantPower\VariantPowerId;
use PhpOption\None;
use PhpOption\Some;

class PowerMother
{
    public static function unassigned(): Power
    {
        return new Power(
            PowerId::generate(),
            None::create(),
            VariantPowerId::generate(),
            false
        );
    }

    public static function assigned(): Power
    {
        return new Power(
            PowerId::generate(),
            Some::create(PlayerId::generate()),
            VariantPowerId::generate(),
            false
        );
    }
}
