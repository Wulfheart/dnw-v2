<?php

namespace Dnw\Foundation\Rule;

readonly class Rule implements RuleInterface
{
    /** @var array<RuleInterface> */
    private array $dependentRules;

    public function __construct(
        private string $key,
        private bool $fails,
        RuleInterface ...$dependentRules
    ) {
        $this->dependentRules = $dependentRules;
    }

    public function passes(): bool
    {
        if ($this->fails) {
            return false;
        }
        foreach ($this->dependentRules as $rule) {
            if (! $rule->passes()) {
                return false;
            }
        }

        return true;
    }

    public function failingKeys(): array
    {
        $keys = [$this->key];
        if ($this->fails) {
            return $keys;
        }

        foreach ($this->dependentRules as $dependentRule) {
            $keys = array_merge($keys, $dependentRule->failingKeys());
        }

        return $keys;
    }

    public function calculate(): void
    {
        foreach ($this->dependentRules as $rule) {
            $rule->calculate();
        }
    }
}
