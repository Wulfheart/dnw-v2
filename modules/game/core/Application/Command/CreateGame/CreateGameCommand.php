<?php

namespace Dnw\Game\Core\Application\Command\CreateGame;

use Dnw\Foundation\Identity\Id;

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
        public ?Id $selectedPowerId,
        public bool $isRanked,
        public bool $isAnonymous,
        public bool $usesCustomMessageMode,
        public ?CustomMessageModePermissions $customMessageModePermissions,
        public ?string $messageModeId,
        public ?int $numberOfSupplyCentersToWin,
        public ?int $maximumNumberOfRounds,
        /** @var array<int> $weekdaysWithoutAdjudication */
        public array $weekdaysWithoutAdjudication,
        public Id $creatorId,
    ) {

    }
}
