<?php

namespace Dnw\Foundation\ViewModel;

class Option extends ViewModel{
    public function __construct(
        public string $value,
        public string $label,
        public bool $selected,
    )
    {

    }
}
