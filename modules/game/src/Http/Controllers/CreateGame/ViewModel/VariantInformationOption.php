<?php

namespace Dnw\Game\Http\Controllers\CreateGame\ViewModel;

use Dnw\Foundation\ViewModel\Option;
use Dnw\Foundation\ViewModel\ViewModel;

class VariantInformationOption extends ViewModel
{
    public function __construct(
        public string $value,
        public string $name,
        public bool $selected,
        public string $description,
        /** @var array<Option> $variant_powers */
        public array $variant_powers,
    ) {

    }
}
