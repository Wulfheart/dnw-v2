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

    public function testCopy(): void
    {
        $class = new class() {
            public int $foo = 1;

            public int $bar = 2;

            public function set(int $i): void
            {
                $this->foo = $this->bar;
                $this->bar = $i;
            }
        };

        $class->set(4);

        $this->assertEquals(2, $class->foo);
        $this->assertEquals(4, $class->bar);
    }
}
