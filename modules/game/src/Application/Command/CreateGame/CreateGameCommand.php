<?php

namespace Dnw\Game\Application\Command\CreateGame;

use Dnw\Foundation\Bus\Interface\Command;
use Dnw\Foundation\Identity\Id;
use Wulfheart\Option\Option;

/**
 * @implements Command<CreateGameCommandResult>
 */
readonly class CreateGameCommand implements Command
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
