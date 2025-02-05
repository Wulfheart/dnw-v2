<?php

namespace App\Web\Form;

use App\Web\Form\Fields\FieldInterface;
use Illuminate\View\View;

class Form
{
    public function __construct(
        public string $action,
        public string $submitText,
        public FormMethodEnum $method = FormMethodEnum::POST,
        /** @var array<FieldInterface> */
        public array $fields = [],
    ) {}

    public function render(): View
    {
        return view('fields.form', ['vm' => $this]);
    }
}
