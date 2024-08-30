<?php

namespace Dnw\Game\Core\Application\Query\GetGameById\Dto;

class PhasesDto {
    public function __construct(
        /** @var array<PhaseDto> $phases */
        private array $phases,
    )
    {
    }
}
