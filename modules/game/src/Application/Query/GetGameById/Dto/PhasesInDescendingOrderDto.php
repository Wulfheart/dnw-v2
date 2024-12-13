<?php

namespace Dnw\Game\Application\Query\GetGameById\Dto;

use Wulfheart\Option\Option;

class PhasesInDescendingOrderDto
{
    public function __construct(
        /** @var array<PhaseDto> $phases */
        public array $phases,
    ) {}

    /**
     * @return Option<PhaseDto>
     */
    public function getCurrentPhase(): Option
    {
        return Option::fromNullable($this->phases[array_key_first($this->phases)] ?? null);
    }
}
