<?php

namespace App\Web\Form\Fields;

use Illuminate\View\View;

class Heading implements FieldInterface
{
    public function __construct(
        public string $text
    ) {}

    public function render(): View
    {
        return view('fields.heading', ['vm' => $this]);
    }
}
