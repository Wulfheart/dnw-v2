<?php

namespace Dnw\Game\Core\Domain\Entity;

use Dnw\Game\Core\Domain\ValueObject\PlayerId;
use Dnw\Game\Core\Domain\ValueObject\Power\PowerId;
use Dnw\Game\Core\Domain\ValueObject\Variant\VariantPower\VariantPowerId;
use PhpOption\Option;

class Power
{
    public function __construct(
        public PowerId $powerId,
        /** @var Option<PlayerId> $playerId */
        public Option $playerId,
        public VariantPowerId $variantPowerId,
        public bool $isDefeated,
    ) {

    }
}
