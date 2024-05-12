<?php

namespace Dnw\Foundation\Rule;

readonly class Rule implements RuleInterface
{
    public function __construct(
        private string $key,
        private bool $fails,
    ) {
    }

    public function passes(): bool
    {
        return $this->fails;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function calculate(): void
    {
    }
}
