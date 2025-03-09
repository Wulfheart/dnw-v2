<?php

namespace Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use ReflectionClass;
use Tests\Attribute\Setup;

abstract class LaravelTestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $reflectionClass = new ReflectionClass($this);

        foreach ($reflectionClass->getMethods() as $method) {
            if ($method->getAttributes(Setup::class)) {
                $method->invoke($this);
            }
        }
    }
}
