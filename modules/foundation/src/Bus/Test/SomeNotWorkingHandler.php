<?php

namespace Dnw\Foundation\Bus\Test;

class SomeNotWorkingHandler
{
    public function __construct(
        // @phpstan-ignore-next-line
        private NoBindingInterface $noBindingInterface
    ) {}
}
