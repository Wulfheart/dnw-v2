<?php

namespace Dnw\Game\Application\Query\GetGameById;

use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\DateTime\DateTime;
use Dnw\Foundation\Identity\Id;
use Dnw\Game\Application\Query\GetGameById\Dto\GameStateEnum;
use Dnw\Game\Application\Query\GetGameById\Dto\PhasesDto;
use Dnw\Game\Application\Query\GetGameById\Dto\VariantPowerDataDto;

class GetGameByIdQueryResultData
{
    public function __construct(
        public Id $id,
        public GameStateEnum $state,
        public string $name,
        public string $variantName,
        public Id $variantId,
        public int $phaseLengthInMinutes,
        public DateTime $nextPhaseStart,
        /** @var ArrayCollection<VariantPowerDataDto> $variantPowerData */
        public ArrayCollection $variantPowerData,
        public PhasesDto $phases,
    ) {}
}
