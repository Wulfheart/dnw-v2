<?php

namespace Dnw\Adjudicator\Dto;

class AdjudicateGameRequest extends Base
{
    public function __construct(
        public string $previous_state_encoded,
        /** @var array<Order> */
        public array $orders,
        public int $scs_to_win,
    ) {

    }
}
