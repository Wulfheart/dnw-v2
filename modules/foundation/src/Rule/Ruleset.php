<?php

namespace Dnw\Foundation\Rule;

class Ruleset
{
    /** @var array<RuleInterface> */
    private array $rules;

    public function __construct(
        RuleInterface ...$rules
    ) {
        $this->rules = [];
        foreach ($rules as $rule) {
            $rule->calculate();
            $this->rules[] = $rule;
        }
    }

    /**
     * @return array<RuleInterface>
     */
    public function rules(): array
    {
        return $this->rules;
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

    public function containsViolation(string $key): bool
    {
        foreach ($this->rules as $rule) {
            if (! $rule->passes() && in_array($key, $rule->failingKeys())) {
                return true;
            }
        }

        return false;
    }
}
