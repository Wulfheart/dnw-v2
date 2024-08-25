<?php

namespace Dnw\Foundation\Form;


use Illuminate\View\View;

class Form
{
    public function __construct(
        public string $action,
        public string $submitText,
        public FormMethodEnum $method = FormMethodEnum::POST,
        /** @var array<Fields\FieldInterface> */
        public array $fields = [],
    ) {}

    public function render(): View
    {
        return view('foundation::fields.form', ['vm' => $this]);
    }
}
