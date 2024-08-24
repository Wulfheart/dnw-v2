<?php

namespace Std;

/**
 * @template TSuccess
 * @template TError
 *
 * @phpstan-consistent-constructor
 */
class Result
{
    private function __construct(
        private bool $ok,
        private mixed $value,
        private mixed $error = null,
    ) {}

    /**
     * @param  TSuccess|null  $value
     */
    public static function ok(mixed $value = null): static
    {
        return new static(true, $value);
    }

    /**
     * @param  TError  $error
     */
    public static function err(mixed $error): static
    {
        return new static(false, null, $error);
    }

    public function isOk(): bool
    {
        return $this->ok;
    }

    public function hasErr(): bool
    {
        return ! $this->ok;
    }

    /**
     * @throws ResultUnwrapException
     *
     * @return TSuccess
     */
    public function unwrap(): mixed
    {
        if ($this->hasErr()) {
            throw new ResultUnwrapException('Called `unwrap` on an `Err` value');
        }

        return $this->value;
    }

    /**
     * @throws ResultUnwrapException
     *
     * @return TError
     */
    public function unwrapErr(): mixed
    {
        if ($this->isOk()) {
            throw new ResultUnwrapException('Called `unwrapErr` on an `Ok` value');
        }

        return $this->error;
    }
}
