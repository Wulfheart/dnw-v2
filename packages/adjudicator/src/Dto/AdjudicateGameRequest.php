<?php

namespace Dnw\Adjudicator\Dto;

class AdjudicateGameRequest implements AdjudicatorDataInterface
{
    public function __construct(
        public string $previous_state_encoded,
        /** @var array<Order> */
        public array $orders,
        public int $scs_to_win,
    ) {

    }

    public static function fromArray(array $array): self
    {
        return new self(
            $array['previous_state_encoded'],
            array_map(fn ($order) => Order::fromArray($order), $array['orders']),
            $array['scs_to_win'],
        );
    }

    public function jsonSerialize(): mixed
    {
        return [
            'previous_state_encoded' => $this->previous_state_encoded,
            'orders' => array_map(fn ($order) => $order->jsonSerialize(), $this->orders),
            'scs_to_win' => $this->scs_to_win,
        ];
    }
}
