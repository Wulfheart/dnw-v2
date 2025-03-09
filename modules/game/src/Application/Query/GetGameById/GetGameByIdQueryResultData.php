<?php

namespace Dnw\Game\Application\Query\GetGameById;

use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\DateTime\DateTime;
use Dnw\Foundation\Identity\Id;
use Dnw\Game\Application\Query\GetGameById\Dto\PhasesInDescendingOrderDto;
use Dnw\Game\Application\Query\GetGameById\Dto\VariantPowerDataDto;
use Dnw\Game\Application\Query\Shared\Game\GameInfo\GameStateEnum;

class GetGameByIdQueryResultData
{
    public function __construct(
        public Id $id,
        public GameStateEnum $state,
        public string $name,
        public string $variantName,
        public string $variantId,
        public int $phaseLengthInMinutes,
        public DateTime $nextPhaseStart,
        /** @var ArrayCollection<VariantPowerDataDto> $variantPowerData */
        public ArrayCollection $variantPowerData,
        public PhasesInDescendingOrderDto $phases,
        public bool $canJoin,
        public bool $canLeave,
    ) {}

    /**
     * @return list<Id>
     */
    public function getPlayerIds(): array
    {
        return $this->variantPowerData->map(fn (VariantPowerDataDto $variantPowerDataDto) => $variantPowerDataDto->playerId->unwrap())->toArray();
    }
}
