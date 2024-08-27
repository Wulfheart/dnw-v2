<?php

namespace Dnw\Foundation\Differ;

use PHPUnit\Framework\Assert;

class Differ
{
    /**
     * @param  array<mixed>  $first
     * @param  array<mixed>  $second
     */
    private function __construct(
        private array $first,
        private array $second
    ) {}

    public static function make(mixed $first, mixed $second): self
    {
        return new self((array) $first, (array) $second);
    }

    public function drop(string $key): self
    {
        unset($this->first[$key], $this->second[$key]);

        return $this;
    }

    public function assertEquality(): void
    {
        Assert::assertEquals($this->first, $this->second);
    }
}
