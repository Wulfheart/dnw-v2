<?php
/** @var \App\Web\Form\Fields\NumberInput $vm */
?>

<div>
    <x-input :label="$vm->label" :key="$vm->key">
        <input type="number" name="{{ $vm->key }}" value="{{ old($vm->key, $vm->defaultValue) }}"
               @isset($vm->min)min="{{ $vm->min }}" @endisset
               @isset($vm->max)max="{{ $vm->max }}" @endisset>
    </x-input>
</div>
