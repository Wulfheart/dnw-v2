<?php

namespace Dnw\Foundation\Bus;

use Dnw\Foundation\Adapter\SleepProviderInterface;
use Dnw\Foundation\Aggregate\NewerAggregateVersionAvailableException;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(RetryIfNewerAggregateVersionIsAvailableMiddleware::class)]
class RetryIfNewerAggregateVersionIsAvailableMiddlewareTest extends TestCase
{
    public function test_does_not_have_any_backoff_if_command_works(): void
    {
        $sleepProviderMock = $this->createMock(SleepProviderInterface::class);

        $sleepProviderMock->expects($this->never())->method('sleep');

        $middleware = new RetryIfNewerAggregateVersionIsAvailableMiddleware($sleepProviderMock);

        $middleware->execute(new stdClass(), fn ($command) => $command);
    }

    public function test_does_not_have_any_backoff_on_unrelated_exception(): void
    {
        $sleepProviderMock = $this->createMock(SleepProviderInterface::class);

        $sleepProviderMock->expects($this->never())->method('sleep');

        $middleware = new RetryIfNewerAggregateVersionIsAvailableMiddleware($sleepProviderMock);

        $this->expectException(InvalidArgumentException::class);
        $middleware->execute(new stdClass(), fn ($command) => throw new InvalidArgumentException());
    }

    public function test_has_n_tries_with_backoff_before_throwing_exception(): void
    {
        $sleepProviderMock = $this->createMock(SleepProviderInterface::class);

        $sleepProviderMock->expects($this->exactly(4))
            ->method('sleep')
            ->with(
                [10],
                [10],
                [20],
                [100]
            );

        $middleware = new RetryIfNewerAggregateVersionIsAvailableMiddleware($sleepProviderMock);

        $this->expectException(NewerAggregateVersionAvailableException::class);
        $middleware->execute(new stdClass(), fn ($command) => throw new NewerAggregateVersionAvailableException());
    }

    public function test_stops_when_command_works(): void
    {
        $sleepProviderMock = $this->createMock(SleepProviderInterface::class);

        $sleepProviderMock->expects($this->exactly(2))
            ->method('sleep')
            ->with(
                ... $this->withConsecutive(
                    [10],
                    [10],
                )
            );

        $command = new class() {
            public int $i = 0;
        };

        $middleware = new RetryIfNewerAggregateVersionIsAvailableMiddleware($sleepProviderMock);

        $middleware->execute($command, function ($command) {
            if ($command->i === 2) {
                return $command;
            }
            $command->i++;
            throw new NewerAggregateVersionAvailableException();
        });
    }
}
