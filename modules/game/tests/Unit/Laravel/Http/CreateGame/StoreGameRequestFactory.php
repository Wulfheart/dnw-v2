<?php

namespace Dnw\Game\Tests\Unit\Laravel\Http\CreateGame;

use Dnw\Foundation\Request\RequestFactory;
use Dnw\Game\Http\CreateGame\StoreGameRequest;

class StoreGameRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            StoreGameRequest::KEY_NAME => $this->faker->name,
            StoreGameRequest::PHASE_LENGTH_IN_MINUTES => $this->faker->numberBetween(10, 1440),
            StoreGameRequest::KEY_JOIN_LENGTH_IN_DAYS => $this->faker->numberBetween(1, 365),
            StoreGameRequest::KEY_START_WHEN_READY => $this->faker->boolean,
            StoreGameRequest::KEY_VARIANT_ID => $this->faker->uuid,
        ];
    }
}
