<?php

namespace Dnw\Adjudicator\Dto;

class AdjudicateGameResponse extends Base
{
    public function __construct(

    ) {

    }

    /** @var AppliedOrder[] */
    public array $applied_orders;

    public string $current_state_encoded;

    public string $phase_long;

    /** @var PhasePowerData[] */
    public array $phase_power_data;

    public string $phase_short;

    public string $phase_type;

    /** @var PossibleOrder[] */
    public array $possible_orders;

    public string $svg_adjudicated;

    public string $svg_with_orders;

    /** @var string[] */
    public array $winners;

    public string $winning_phase;
}
