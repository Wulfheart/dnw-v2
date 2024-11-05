<?php

namespace App\Web\Game\GamePanel\ViewModel;

class PlayerInfoViewModel
{
    public function __construct(
        public string $name,
        public string $link,
    ) {}
}
