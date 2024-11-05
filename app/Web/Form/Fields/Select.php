<?php

namespace App\Web\Form\Fields;

use Illuminate\View\View;

class Select implements FieldInterface
{
    public string $defaultValue;

    public function __construct(
        public string $key,
        public string $label,
        public ?string $description = null,
        /** @var array<SelectOption> $options */
        public array $options = [],
    ) {
        foreach ($options as $option) {
            if ($option->selected) {
                $this->defaultValue = $option->value;
            }
        }
    }

    public function render(): View
    {
        return view('foundation::fields.select', ['vm' => $this]);
    }
}
