<?php

namespace Dnw\Foundation\State;

class State {
    public function __construct(
        /** @var array<string, mixed> $state */
        private array $state = []
    )
    {

    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->state[$key] ?? $default;
    }
}
