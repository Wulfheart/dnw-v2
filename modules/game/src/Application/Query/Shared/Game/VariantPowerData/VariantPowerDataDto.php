<?php

namespace Dnw\Game\Application\Query\Shared\Game\VariantPowerData;

use Wulfheart\Option\Option;

final readonly class VariantPowerDataDto
{
    public function __construct(
        public string $name,
        /** @var Option<string> $playerId */
        public Option $playerId,
        public int $supplyCenterCount,
        public int $unitCount,
        public VariantPowerStatusEnum $status,
    ) {}
}
