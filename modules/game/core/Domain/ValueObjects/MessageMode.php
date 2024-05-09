<?php

namespace ValueObjects;

final readonly class MessageMode
{
    public function __construct(
        public ?string $name,
        public bool $is_custom,
    ) {

    }
}
