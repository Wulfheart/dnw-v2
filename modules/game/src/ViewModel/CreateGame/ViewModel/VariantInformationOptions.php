<?php

namespace Dnw\Game\ViewModel\CreateGame\ViewModel;

use Dnw\Foundation\ViewModel\ViewModel;

class VariantInformationOptions extends ViewModel
{
    public function __construct(
        /** @var array<VariantInformationOption> $options */
        public array $options = []
    ) {}
}
