<?php

namespace App\Foundation\Request\Test;

use App\Foundation\Request\RequestFactory;

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
