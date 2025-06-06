<?php

namespace Dnw\Game\Application\Query\GetGameById\Dto;

use Dnw\Foundation\Identity\Id;
use Wulfheart\Option\Option;

/**
 * @codeCoverageIgnore
 */
class VariantPowerDataDto
{
    public function __construct(
        public string $variantPowerId,
        /** @var Option<Id> $playerId */
        public Option $playerId,
        public string $name,
        public string $color,
    ) {}
}
