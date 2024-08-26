<?php

namespace Dnw\Foundation\Request;

use Dnw\Foundation\Request\Test\TestRequestFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RequestFactory::class)]
class RequestFactoryTest extends TestCase
{
    public function test_new_and_create(): void
    {
        $result = TestRequestFactory::new()->create();
        $this->assertEquals(['name' => 'Hallo', 'foo' => 'bar'], $result);
    }

    public function test_without(): void
    {
        $result = TestRequestFactory::new()->without('name')->create();
        $this->assertEquals(['foo' => 'bar'], $result);
    }

    public function test_state(): void
    {
        $result = TestRequestFactory::new()->state(['name' => 'World'])->create();
        $this->assertEquals(['name' => 'World', 'foo' => 'bar'], $result);

        $result = TestRequestFactory::new()->state(['name' => 'World'])->state(['foo' => 'baz'])->create();
        $this->assertEquals(['name' => 'World', 'foo' => 'baz'], $result);

        $result = TestRequestFactory::new()->state(['Hello' => 'World'])->create();
        $this->assertEquals(['name' => 'Hallo', 'foo' => 'bar', 'Hello' => 'World'], $result);
    }

    public function test_override(): void
    {
        $result = TestRequestFactory::new()->override('name', 'World')->create();
        $this->assertEquals(['name' => 'World', 'foo' => 'bar'], $result);

        $result = TestRequestFactory::new()->override('name', 'World')->override('foo', 'baz')->create();
        $this->assertEquals(['name' => 'World', 'foo' => 'baz'], $result);
    }

    public function test_new_instantiation(): void
    {
        $original = TestRequestFactory::new();
        $this->assertEquals(['name' => 'Hallo', 'foo' => 'bar'], $original->create());

        $new = $original->override('name', 'World');
        $this->assertEquals(['name' => 'World', 'foo' => 'bar'], $new->create());
        $this->assertEquals(['name' => 'Hallo', 'foo' => 'bar'], $original->create());

        $new = $original->state(['name' => 'World']);
        $this->assertEquals(['name' => 'World', 'foo' => 'bar'], $new->create());
        $this->assertEquals(['name' => 'Hallo', 'foo' => 'bar'], $original->create());

        $new = $original->without('name');
        $this->assertEquals(['foo' => 'bar'], $new->create());
        $this->assertEquals(['name' => 'Hallo', 'foo' => 'bar'], $original->create());
    }
}
