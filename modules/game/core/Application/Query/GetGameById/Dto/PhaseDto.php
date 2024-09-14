<?php

namespace Dnw\Game\Core\Application\Query\GetGameById\Dto;

use Dnw\Foundation\DateTime\DateTime;
use Std\Option;

class PhaseDto
{
    public function __construct(
        public string $name,
        public string $type,
        public DateTime $adjudicationTime,
        /** @var Option<string> $linkToSvgWithOrders */
        public Option $linkToSvgWithOrders,
        /** @var Option<string> $linkToAdjudicatedSvg */
        public Option $linkToAdjudicatedSvg,
    ) {}
}
