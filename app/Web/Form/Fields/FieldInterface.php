<?php

namespace App\Web\Form\Fields;

use Illuminate\View\View;

interface FieldInterface
{
    public function render(): View;
}
