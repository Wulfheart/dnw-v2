<?php

namespace App\Web\Form\Fields;

use Illuminate\View\View;

class Separator implements FieldInterface
{
    public function render(): View
    {
        return view('foundation::fields.separator', []);
    }
}
