<?php

namespace App\Web\Form\Fields;

class SelectOption
{
    public function __construct(
        public string $value,
        public string $name,
        public bool $selected,
    ) {}
}