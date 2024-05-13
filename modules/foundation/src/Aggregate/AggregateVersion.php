<?php

namespace Dnw\Foundation\Aggregate;

readonly class AggregateVersion
{
    public function __construct(
        private readonly int $version
    ) {
    }

    public function isLessThan(AggregateVersion $other): bool
    {
        return $this->version < $other->version;
    }

    public function isGreaterThan(AggregateVersion $other): bool
    {
        return $this->version > $other->version;
    }

    public function increment(): AggregateVersion
    {
        return new AggregateVersion($this->version + 1);
    }
}
