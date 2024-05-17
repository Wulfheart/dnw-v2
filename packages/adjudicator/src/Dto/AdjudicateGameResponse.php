<?php

namespace Dnw\Adjudicator\Dto;

use Dnw\Foundation\Collection\ArrayCollection;

class AdjudicateGameResponse implements AdjudicatorDataInterface
{
    public function __construct(
        /** @var array<AppliedOrder> */
        public array $applied_orders,
        public string $current_state_encoded,
        public string $phase_long,
        /** @var array<PhasePowerData> */
        public array $phase_power_data,
        public string $phase_short,
        public string $phase_type,
        /** @var array<PossibleOrder> */
        public array $possible_orders,
        public string $svg_adjudicated,
        public string $svg_with_orders,
        /** @var array<string> */
        public array $winners,
        public string $winning_phase,
    ) {

    }

    public function getPossibleOrdersByPowerName(string $powerName): PossibleOrder
    {
        return ArrayCollection::fromArray($this->possible_orders)->findBy(
            fn ($possibleOrder) => $possibleOrder->power === $powerName
        )->get();
    }

    /**
     * @return array<string>
     */
    public function getAppliedOrdersByPowerName(string $powerName): array
    {
        return ArrayCollection::fromArray($this->applied_orders)->findBy(
            fn ($appliedOrder) => $appliedOrder->power === $powerName
        )->map(
            fn (AppliedOrder $appliedOrder) => $appliedOrder->orders
        )->getOrElse([]);
    }

    public function getPhasePowerDataByPowerName(string $powerName): PhasePowerData
    {
        return ArrayCollection::fromArray($this->phase_power_data)->findBy(
            fn ($phasePowerData) => $phasePowerData->power === $powerName
        )->get();
    }

    public function powerHasWon(string $powerName): bool
    {
        return in_array($powerName, $this->winners);
    }

    public static function fromArray(array $array): AdjudicateGameResponse
    {
        return new self(
            array_map(fn ($applied_order) => AppliedOrder::fromArray($applied_order), $array['applied_orders']),
            $array['current_state_encoded'],
            $array['phase_long'],
            array_map(fn ($phase_power_data) => PhasePowerData::fromArray($phase_power_data), $array['phase_power_data']),
            $array['phase_short'],
            $array['phase_type'],
            array_map(fn ($possible_order) => PossibleOrder::fromArray($possible_order), $array['possible_orders']),
            $array['svg_adjudicated'],
            $array['svg_with_orders'],
            $array['winners'],
            $array['winning_phase'],
        );
    }

    public function jsonSerialize(): mixed
    {
        return [
            'applied_orders' => $this->applied_orders,
            'current_state_encoded' => $this->current_state_encoded,
            'phase_long' => $this->phase_long,
            'phase_power_data' => $this->phase_power_data,
            'phase_short' => $this->phase_short,
            'phase_type' => $this->phase_type,
            'possible_orders' => $this->possible_orders,
            'svg_adjudicated' => $this->svg_adjudicated,
            'svg_with_orders' => $this->svg_with_orders,
            'winners' => $this->winners,
            'winning_phase' => $this->winning_phase,
        ];
    }
}
