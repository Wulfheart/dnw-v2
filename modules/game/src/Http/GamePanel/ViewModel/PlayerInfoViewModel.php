<?php

namespace Dnw\Game\Http\GamePanel\ViewModel;

class PlayerInfoViewModel
{
    public function __construct(
        public string $name,
        public string $link,
    ) {}
}
