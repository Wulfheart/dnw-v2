<?php

namespace Dnw\Game\Tests;

class FooClass
{
    public function bar(): string
    {
        return $this->foo();
    }

    public function foo(): string
    {
        return 'foo';
    }
}
