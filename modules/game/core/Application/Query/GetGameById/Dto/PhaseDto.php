<?php

namespace Dnw\Game\Core\Application\Query\GetGameById\Dto;

use Dnw\Foundation\DateTime\DateTime;

class PhaseDto {
    public function __construct(
        public string $name,
        public string $type,
        public DateTime $adjudicationTime
    )
    {
    }

}
