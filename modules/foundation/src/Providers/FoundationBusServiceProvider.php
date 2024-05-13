<?php

namespace Dnw\Foundation\Providers;

use Dnw\Foundation\Adapter\SleepProvider;
use Dnw\Foundation\Adapter\SleepProviderInterface;
use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\Bus\LaravelHandlerLocator;
use Dnw\Foundation\Bus\RetryIfNewerAggregateVersionIsAvailableMiddleware;
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
            SleepProviderInterface::class,
            SleepProvider::class
        );
        $this->app->bind(
            BusInterface::class,
            function (Application $application) {
                $inflector = new HandleInflector();
                $extractor = new ClassNameExtractor();
                $handlerLocator = $application->make(LaravelHandlerLocator::class);
                $commandHandlerMiddleware = new CommandHandlerMiddleware(
                    $extractor,
                    $handlerLocator,
                    $inflector
                );

                $retryMiddleware = new RetryIfNewerAggregateVersionIsAvailableMiddleware(
                    $application->make(SleepProviderInterface::class)
                );

                $commandBus = new CommandBus([
                    $commandHandlerMiddleware,
                    $retryMiddleware,
                ]);

                return new TacticianBus($commandBus);
            }
        );
    }
}
