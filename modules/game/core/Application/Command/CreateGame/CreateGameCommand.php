<?php

namespace Dnw\Game\Core\Application\Command\CreateGame;

use Dnw\Foundation\Identity\Id;
use Std\Option;

readonly class CreateGameCommand
{
    public function __construct(
        public Id $gameId,
        public string $name,
        public int $phaseLengthInMinutes,
        public int $joinLengthInDays,
        public bool $startWhenReady,
        public Id $variantId,
        public bool $randomPowerAssignments,
        /** @var Option<Id> */
        public Option $selectedVariantPowerId,
        public bool $isRanked,
        public bool $isAnonymous,
        /** @var array<int> $weekdaysWithoutAdjudication */
        public array $weekdaysWithoutAdjudication,
        public Id $creatorId,
    ) {}
}
