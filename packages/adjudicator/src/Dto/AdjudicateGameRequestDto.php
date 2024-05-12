<?php

namespace Dnw\Adjudicator\Dto;

use Illuminate\Support\Collection;

class AdjudicateGameRequestDto extends BaseDto
{
    public string $previous_state_encoded;

    /** @var \Dnw\Adjudicator\Dto\OrderDto[] $orders */
    #[CastWith(ArrayCaster::class, itemType: OrderDto::class)]
    public Collection $orders;

    public int $scs_to_win;
}
