<?php

namespace App\Navigation;

class NavigationItemViewModel
{
    public function __construct(
        public NavigationItemNameEnum $name,
        public string $label,
        public string $route,
    ) {

    }
}
