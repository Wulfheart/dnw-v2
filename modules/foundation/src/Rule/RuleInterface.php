<?php

namespace Dnw\Foundation\Rule;

interface RuleInterface
{
    public function passes(): bool;

    public function key(): string;

    /*
     * Technically not needed now, but we might need it in the future to validate rules with callbacks
     */
    public function calculate(): void;
}
