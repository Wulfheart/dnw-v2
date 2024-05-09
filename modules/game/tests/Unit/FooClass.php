<?php

namespace Dnw\Game\Tests\Unit;

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
