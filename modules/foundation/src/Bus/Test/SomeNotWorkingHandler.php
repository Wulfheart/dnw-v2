<?php

namespace Dnw\Foundation\Bus\Test;

class SomeNotWorkingHandler
{
    public function __construct(
        private NoBindingInterface $noBindingInterface
    ) {}
}
