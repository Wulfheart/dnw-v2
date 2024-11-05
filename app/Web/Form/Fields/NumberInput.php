<?php

namespace App\Web\Form\Fields;

use Illuminate\View\View;

readonly class NumberInput implements FieldInterface
{
    public function __construct(
        public string $key,
        public string $label,
        public ?string $description = null,
        public ?int $defaultValue = null,
        public ?int $min = null,
        public ?int $max = null,
    ) {}

    public function render(): View
    {
        return view('foundation::fields.number-input', ['vm' => $this]);
    }
}
