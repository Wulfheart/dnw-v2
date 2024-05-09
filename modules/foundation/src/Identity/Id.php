<?php

namespace Dnw\Foundation\Identity;

use Symfony\Component\Uid\Ulid;

final readonly class Id
{
    private function __construct(
        private string $value
    ) {
    }

    public static function fromString(string $value): self
    {
        return new self((new Ulid($value))->toRfc4122());
    }

    public static function generate(): self
    {
        return new self((new Ulid())->toRfc4122());
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
