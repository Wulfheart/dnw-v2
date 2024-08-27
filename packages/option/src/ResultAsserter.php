<?php

namespace Std;

use PHPUnit\Framework\Assert;

class ResultAsserter
{
    // @phpstan-ignore-next-line
    public static function assertOk(Result $result): void
    {
        Assert::assertTrue($result->isOk());
    }

    // @phpstan-ignore-next-line
    public static function assertErr(Result $result): void
    {
        Assert::assertTrue($result->hasErr());
    }
}
