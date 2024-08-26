<?php

namespace Dnw\Foundation\Bus;

use Dnw\Foundation\Bus\Test\SomeCommand;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FakeBus::class)]
class FakeBusTest extends TestCase
{
    public function test_handle_class_string_command(): void
    {
        $command = new class() {};
        $result = 'result';
        $fakeBus = new FakeBus([$command::class, $result]);
        $this->assertEquals($result, $fakeBus->handle($command));
    }

    public function test_handle_object_command(): void
    {

        $result = 'result';

        $fakeBus = new FakeBus([new SomeCommand(), $result]);

        $this->assertEquals($result, $fakeBus->handle(new SomeCommand()));
    }

    public function test_handle_callable(): void
    {
        $result = 'result';
        $command = ['foo'];

        $fakeBus = new FakeBus([fn ($command) => $command === ['foo'], $result]);

        $this->assertEquals($result, $fakeBus->handle($command));
    }

    public function test_throws_exception_if_command_is_not_found(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Command not found');

        $fakeBus = new FakeBus();
        $fakeBus->handle(new class() {});
    }
}
