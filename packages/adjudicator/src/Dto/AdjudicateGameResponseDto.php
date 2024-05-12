<?php

namespace Dnw\Adjudicator\Dto;

use Illuminate\Support\Collection;

class AdjudicateGameResponseDto extends BaseDto
{
    /** @var \Dnw\Adjudicator\Dto\AppliedOrderDto[] */
    public Collection $applied_orders;

    public string $current_state_encoded;

    public string $phase_long;

    /** @var \Dnw\Adjudicator\Dto\PhasePowerDataDto[] */
    public Collection $phase_power_data;

    public string $phase_short;

    public string $phase_type;

    /** @var \Dnw\Adjudicator\Dto\PossibleOrderDto[] */
    public Collection $possible_orders;

    public string $svg_adjudicated;

    public string $svg_with_orders;

    /** @var string[] */
    public array $winners;

    public string $winning_phase;
}
