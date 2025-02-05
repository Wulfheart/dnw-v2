<?php

namespace Dnw\Foundation\Bus;

use League\Tactician\CommandBus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TacticianBus::class)]
class TacticianBusTest extends TestCase
{
    public function test_handle(): void
    {
        $commandBus = $this->createMock(CommandBus::class);

        $this->markTestIncomplete('TODO');
        // $commandBus->method('handle')
        //     ->willReturn('result');
        //
        // $tacticianBus = new TacticianBus($commandBus);
        //
        // $this->assertEquals('result', $tacticianBus->handle('foo'));
    }
}
