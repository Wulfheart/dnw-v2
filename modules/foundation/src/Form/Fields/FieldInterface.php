<?php

namespace Dnw\Foundation\Form\Fields;

use Illuminate\View\View;

interface FieldInterface
{
    public function render(): View;
}
