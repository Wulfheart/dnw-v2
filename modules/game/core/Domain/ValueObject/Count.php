<?php

namespace ValueObjects;

final class Count
{
    public static function zero(): self
    {
        return new self(0);
    }
}
