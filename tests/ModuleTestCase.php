<?php

namespace Tests;

use Dnw\Foundation\Bus\BusInterface;
use Dnw\Foundation\Identity\Id;
use Dnw\Foundation\UserContext\UserContext;
use Wulfheart\Option\Option;

class ModuleTestCase extends LaravelTestCase
{
    protected BusInterface $bus;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bus = $this->app->make(BusInterface::class);
        $this->app->bind(UserContext::class, fn () => new UserContext(Option::none()));
    }

    /**
     * @param  array<string>  $permissions
     */
    public function bindUser(Id $id, array $permissions = []): void
    {
        $this->app->bind(UserContext::class, fn () => new UserContext(Option::some($id), $permissions));
    }

    /**
     * @template T
     *
     * @param  class-string<T>  $type
     * @return T
     */
    protected function bootstrap(string $type): mixed
    {
        return $this->app->make($type);
    }
}
