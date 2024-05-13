<?php

namespace Dnw\Game\Tests\Unit;

use PHPUnit\Framework\TestCase;

class SomeTest extends TestCase
{
    public function testSomething(): void
    {
        $mock = $this->createPartialMock(FooClass::class, ['foo']);
        $mock->expects($this->exactly(1))
            ->method('foo')
            ->willReturn('huu');

        $this->assertEquals('huu', $mock->bar());

    }
}
