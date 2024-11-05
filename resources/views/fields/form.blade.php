<?php

use App\Web\Form\Form;

/** @var Form $vm */
?>

<form action="{{ $vm->action }}" method="POST" class="web">
    @csrf
    @method($vm->method->value)
    @foreach ($vm->fields as $field)
        {!! $field->render() !!}
    @endforeach
    <input type="submit" value="{{ $vm->submitText }}">
</form>
