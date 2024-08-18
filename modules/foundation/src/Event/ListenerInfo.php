<?php

namespace Dnw\Foundation\Event;

class ListenerInfo
{
    public function __construct(
        /** @var class-string $class */
        public string $class,
        public string $method,
        public bool $isAsync,
    ) {}

    /**
     * @param  array<string, mixed>  $arr
     */
    public static function __set_state(array $arr): self
    {
        return new self(
            $arr['class'],
            $arr['method'],
            $arr['isAsync'],
        );
    }
}
