<?php

namespace Module;

abstract class Module
{
    /**
     * @return array<class-string<object>, array<class-string<object>>
     */
    protected array $integrationListeners = [];

    /**
     * @return array<class-string<object>, array<class-string<object>>
     */
    protected array $domainListeners = [];

    public function make(): static
    {
        return new static();
    }
}
