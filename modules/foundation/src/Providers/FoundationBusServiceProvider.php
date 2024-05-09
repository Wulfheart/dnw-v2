<?php

namespace Dnw\Foundation\Providers;

use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\Bus\LaravelHandlerLocator;
use Dnw\Foundation\Bus\TacticianBus;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;

class FoundationBusServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            BusInterface::class,
            function (Application $application) {
                $inflector = new HandleInflector();
                $extractor = new ClassNameExtractor();
                $handlerLocator = $application->make(LaravelHandlerLocator::class);
                $middleware = new CommandHandlerMiddleware(
                    $extractor,
                    $handlerLocator,
                    $inflector
                );
                $commandBus = new CommandBus([
                    $middleware,
                ]);

                return new TacticianBus($commandBus);
            }
        );
    }
}
