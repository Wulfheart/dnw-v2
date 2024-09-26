<?php

namespace Dnw\Foundation\Identity;

use Symfony\Component\Uid\Ulid;
use Wulfheart\Option\Option;

readonly class Id
{
    private Ulid $ulid;

    private function __construct(
        string $value
    ) {
        $this->ulid = new Ulid($value);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    /**
     * @return Option<Id>
     */
    public static function fromNullable(?string $value): Option
    {
        if ($value === null) {
            return Option::none();
        }

        return Option::some(new self($value));
    }

    public static function generate(): self
    {
        return new self(new Ulid());
    }

    public function equals(Id $other): bool
    {
        return $this->ulid->equals($other->ulid);
    }

    public function __toString(): string
    {
        return (string) $this->ulid;
    }
}
