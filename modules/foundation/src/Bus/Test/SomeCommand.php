<?php

namespace Dnw\Foundation\Bus\Test;

use Dnw\Foundation\Bus\Interface\Command;

/**
 * @implements Command<string>
 */
class SomeCommand implements Command
{
    public function __construct(
        public string $name = 'foo'
    ) {}
}
