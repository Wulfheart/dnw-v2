<?php

use App\Web\Form\Fields\Select;

/** @var Select $vm */
?>

<div>
    <x-input :label="$vm->label" :key="$vm->key">
        <select name="{{ $vm->key }}">
            @foreach ($vm->options as $option)
                <option value="{{ $option->value }}" @selected($option->value == old($vm->key, $vm->defaultValue))>
                    {{ $option->name }}
                </option>
            @endforeach
        </select>
    </x-input>
</div>
