<?php

namespace Dnw\Foundation\Form\Fields;

use Illuminate\View\View;

class Heading implements FieldInterface
{
    public function __construct(
        public string $text
    ) {}

    public function render(): View
    {
        return view('foundation::fields.heading', ['vm' => $this]);
    }
}
