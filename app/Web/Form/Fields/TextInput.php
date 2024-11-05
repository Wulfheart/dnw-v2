<?php

namespace App\Web\Form\Fields;

use Illuminate\View\View;

readonly class TextInput implements FieldInterface
{
    public function __construct(
        public string $key,
        public string $label,
        public ?string $description = null,
        public ?string $defaultValue = null,
    ) {}

    public function render(): View
    {
        return view('fields.text-input', ['vm' => $this]);
    }
}
