<?php

namespace Entity;

use PhpOption\Option;
use ValueObjects\PlayerId;
use ValueObjects\Power\PowerId;
use ValueObjects\Variant\VariantPower\VariantPowerId;

final class Power
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
