<?php

namespace Dnw\Adjudicator;

readonly class Uri
{
    private string $uri;

    public function __construct(
        string $uri
    ) {
        $this->uri = rtrim($uri, '/');
    }

    public function appendToPath(string $appended): self
    {
        return new self($this->uri . '/' . ltrim($appended, '/'));
    }

    public function __toString(): string
    {
        return $this->uri;
    }
}
