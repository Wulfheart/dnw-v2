<?php

namespace Dnw\Foundation\Identity;

use Std\Option;
use Symfony\Component\Uid\Ulid;

trait UlidValueObjectTrait
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

    public static function fromId(Id $id): self
    {
        return new self($id);
    }

    /**
     * @return Option<self>
     */
    public static function fromNullableString(?string $value): Option
    {
        if ($value === null) {
            return Option::none();
        }

        return Option::some(self::fromString($value));
    }

    public static function new(): self
    {
        return new self(new Ulid());
    }

    public function __toString(): string
    {
        return (string) $this->ulid;
    }

    public function toId(): Id
    {
        return Id::fromString((string) $this);
    }
}
