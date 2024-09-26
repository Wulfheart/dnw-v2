<?php

namespace Dnw\Game\Core\Application\Query\GetGameById\Dto;

use Dnw\Foundation\Identity\Id;
use Wulfheart\Option\Option;

class VariantPowerDataDto
{
    public function __construct(
        public Id $variantPowerId,
        /** @var Option<Id> $playerId */
        public Option $playerId,
        public string $name,
        public string $color,
    ) {}
}
