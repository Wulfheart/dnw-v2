<?php

namespace Dnw\Foundation\Request;

abstract class RequestFactory extends \Worksome\RequestFactories\RequestFactory
{
    public function override(string $key, mixed $value): static
    {
        return $this->state([$key => $value]);
    }
}
