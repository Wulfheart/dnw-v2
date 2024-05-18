<?php

namespace Dnw\Foundation\Aggregate;

readonly class AggregateVersion
{
    private const int INITIAL_VERSION = 0;

    public function __construct(
        private readonly int $version
    ) {
    }

    public static function initial(): AggregateVersion
    {
        return new AggregateVersion(self::INITIAL_VERSION);
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

    public function int(): int
    {
        return $this->version;
    }

    public function isInitial(): bool
    {
        return $this->version === self::INITIAL_VERSION;
    }
}
