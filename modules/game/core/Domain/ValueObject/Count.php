<?php

namespace Dnw\Game\Core\Domain\ValueObject;

class Count
{
    public static function zero(): self
    {
        return new self(0);
    }
}
