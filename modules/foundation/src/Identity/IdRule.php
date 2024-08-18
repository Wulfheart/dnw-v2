<?php

namespace Dnw\Foundation\Identity;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Symfony\Component\Uid\Ulid;

class IdRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Ulid::isValid($value)) {
            $fail($attribute . ' is not a valid ULID');
        }
    }
}
