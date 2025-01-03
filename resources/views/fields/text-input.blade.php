<?php

use App\Web\Form\Fields\TextInput;

/** @var TextInput $vm */
?>

<div>
    <x-input :label="$vm->label" :key="$vm->key">
        <input type="text" name="{{ $vm->key }}" value="{{ old($vm->key, $vm->defaultValue) }}">
    </x-input>
</div>
