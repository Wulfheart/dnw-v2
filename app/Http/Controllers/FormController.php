<?php

namespace App\Http\Controllers;

use Dnw\Foundation\Form\Fields\NumberInput;
use Dnw\Foundation\Form\Fields\TextInput;
use Dnw\Foundation\Form\Form;
use Illuminate\Http\Request;

class FormController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $form = new Form(
            action: '/submit',
            submitText: 'Submit',
            fields: [
                new TextInput(key: 'name', label: 'Name'),
                new NumberInput(key: 'age', label: 'Age', min: 0, max: 100),
            ],
        );

        return view('foo', ['form' => $form]);

    }
}
