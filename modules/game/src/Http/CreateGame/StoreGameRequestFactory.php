<?php

namespace Dnw\Game\Http\CreateGame;

use Dnw\Foundation\Identity\Id;
use Dnw\Foundation\Request\RequestFactory;

/**
 * @codeCoverageIgnore
 */
class StoreGameRequestFactory extends RequestFactory
{
    public static function definition(): array
    {
        return [
            StoreGameRequest::KEY_NAME => fake()->name,
            StoreGameRequest::PHASE_LENGTH_IN_MINUTES => fake()->numberBetween(10, 1440),
            StoreGameRequest::KEY_JOIN_LENGTH_IN_DAYS => fake()->numberBetween(1, 365),
            StoreGameRequest::KEY_START_WHEN_READY => fake()->boolean,
            StoreGameRequest::KEY_VARIANT_ID => (string) Id::generate(),
        ];
    }

    public function name(string $name): self
    {
        return $this->override(StoreGameRequest::KEY_NAME, $name);
    }

    public function phaseLengthInMinutes(int $phaseLengthInMinutes): self
    {
        return $this->override(StoreGameRequest::PHASE_LENGTH_IN_MINUTES, $phaseLengthInMinutes);
    }

    public function joinLengthInDays(int $joinLengthInDays): self
    {
        return $this->override(StoreGameRequest::KEY_JOIN_LENGTH_IN_DAYS, $joinLengthInDays);
    }

    public function startWhenReady(bool $startWhenReady): self
    {
        return $this->override(StoreGameRequest::KEY_START_WHEN_READY, $startWhenReady);
    }

    public function variantId(string $variantId): self
    {
        return $this->override(StoreGameRequest::KEY_VARIANT_ID, $variantId);
    }
}
