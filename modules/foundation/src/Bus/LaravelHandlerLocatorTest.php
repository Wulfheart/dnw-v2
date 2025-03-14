<?php

namespace Dnw\Foundation\Bus;

use Dnw\Foundation\Bus\Test\SomeAwesomeHandler;
use Dnw\Foundation\Bus\Test\SomeHandlerInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use League\Tactician\Exception\MissingHandlerException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\LaravelTestCase;

#[CoversClass(LaravelHandlerLocator::class)]

class LaravelHandlerLocatorTest extends LaravelTestCase
{
    public function test_getHandlerForCommand_throws_if_handler_exists_but_cannot_be_built(): void
    {
        $locator = new LaravelHandlerLocator($this->app);
        $this->expectException(BindingResolutionException::class);
        $locator->getHandlerForCommand('Dnw\Foundation\Bus\Test\SomeNotWorking');
    }

    public function test_getHandlerForCommand_builds_if_handler_exists(): void
    {
        $locator = new LaravelHandlerLocator($this->app);
        $handler = $locator->getHandlerForCommand('Dnw\Foundation\Bus\Test\SomeAwesome');
        $this->assertInstanceOf(SomeAwesomeHandler::class, $handler);
    }

    public function test_getHandlerForCommand_throws_if_handler_interface_exists_but_cannot_be_built(): void
    {
        $locator = new LaravelHandlerLocator($this->app);
        $this->expectException(BindingResolutionException::class);
        $locator->getHandlerForCommand('Dnw\Foundation\Bus\Test\Some');
    }

    public function test_getHandlerForCommand_builds_if_handler_interface_exists(): void
    {
        $this->instance(SomeHandlerInterface::class, new SomeAwesomeHandler());
        $locator = new LaravelHandlerLocator($this->app);
        $handler = $locator->getHandlerForCommand('Dnw\Foundation\Bus\Test\Some');
        $this->assertInstanceOf(SomeAwesomeHandler::class, $handler);
    }

    public function test_getHandlerForCommand_throws_if_it_cannot_guess_a_handler(): void
    {
        $locator = new LaravelHandlerLocator($this->app);
        $this->expectException(MissingHandlerException::class);
        $locator->getHandlerForCommand('Dnw\Foundation\Bus\Test\SomeNotExisting');
    }
}
