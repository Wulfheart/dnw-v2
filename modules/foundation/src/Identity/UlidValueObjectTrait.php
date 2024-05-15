<?php

namespace Dnw\Foundation\Identity;

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

    public static function new(): self
    {
        return new self(new Ulid());
    }

    public function __toString(): string
    {
        return $this->ulid->toRfc4122();
    }
}
