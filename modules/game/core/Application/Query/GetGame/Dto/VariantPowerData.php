<?php

namespace Dnw\Game\Core\Application\Query\GetGame\Dto;

use Dnw\Foundation\Identity\Id;

class VariantPowerData
{
    public function __construct(
        public Id $variantPowerId,
        public Id $playerId,
    ) {}
}
