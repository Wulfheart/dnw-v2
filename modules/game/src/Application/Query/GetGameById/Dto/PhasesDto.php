<?php

namespace Dnw\Game\Application\Query\GetGameById\Dto;

class PhasesDto
{
    public function __construct(
        /** @var array<PhaseDto> $phases */
        public array $phases,
    ) {}
}
