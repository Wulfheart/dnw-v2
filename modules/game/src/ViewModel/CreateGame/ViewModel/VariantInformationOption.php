<?php

namespace Dnw\Game\ViewModel\CreateGame\ViewModel;

use Dnw\Foundation\ViewModel\Options;
use Dnw\Foundation\ViewModel\ViewModel;

class VariantInformationOption extends ViewModel
{
    public function __construct(
        public string $value,
        public string $name,
        public bool $selected,
        public string $description,
        public Options $variant_powers,
    ) {}
}
