<?php

namespace Dnw\Game\Tests;

final class SomeTest extends \PHPUnit\Framework\TestCase
{
    public function testSomething()
    {
        $mock = $this->createPartialMock(FooClass::class, ['foo']);
        $mock->expects($this->once())
            ->method('foo')
            ->willReturn('bar');

        $this->assertEquals('bar', $mock->bar());

    }
}
