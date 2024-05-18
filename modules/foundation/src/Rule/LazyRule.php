<?php

namespace Dnw\Foundation\Rule;

use Closure;

class LazyRule implements RuleInterface
{
    /** @var array<RuleInterface> */
    private array $dependentRules;

    public function __construct(
        private string $key,
        /** @var Closure(): bool $fails */
        private Closure $fails,
        RuleInterface ...$dependentRules
    ) {
        $this->dependentRules = $dependentRules;
    }

    public function passes(): bool
    {
        if ($this->fails()) {
            return false;
        }
        foreach ($this->dependentRules as $rule) {
            if (! $rule->passes()) {
                return false;
            }
        }

        return true;
    }

    private function fails(): bool
    {
        return ($this->fails)();
    }

    public function failingKeys(): array
    {
        $keys = [];
        if ($this->fails()) {
            return [$this->key];
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
