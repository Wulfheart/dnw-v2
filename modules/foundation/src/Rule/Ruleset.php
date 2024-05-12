<?php

namespace Dnw\Foundation\Rule;

class Ruleset
{
    /** @var array<RuleInterface> */
    private array $rules;

    public function __construct(
        RuleInterface ...$rules
    ) {
        foreach ($rules as $rule) {
            $rule->calculate();
            $this->rules[] = $rule;
        }
    }

    public function passes(): bool
    {
        foreach ($this->rules as $rule) {
            if (! $rule->passes()) {
                return false;
            }
        }

        return true;
    }

    public function fails(): bool
    {
        return ! $this->passes();
    }

    /**
     * @return array<string>
     */
    public function getErrors(): array
    {
        $errors = [];
        foreach ($this->rules as $rule) {
            if (! $rule->passes()) {
                array_push($errors, ...$rule->failingKeys());
            }
        }

        return $errors;
    }
}
