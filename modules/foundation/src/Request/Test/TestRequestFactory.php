<?php

namespace Dnw\Foundation\Request\Test;

use Dnw\Foundation\Request\RequestFactory;

class TestRequestFactory extends RequestFactory
{
    protected static function definition(): array
    {
        return [
            'name' => 'Hallo',
            'foo' => 'bar',
        ];
    }
}
