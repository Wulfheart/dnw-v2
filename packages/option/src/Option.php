<?php

namespace Std;

/**
 * @template T
 */
final class Option
{
    private function __construct(
        private mixed $value,
        private bool $ok,
    ) {
    }

    /**
     * @template S
     *
     * @param  S|null  $value
     * @return Option<S>
     */
    public static function fromNullable($value): self
    {
        if ($value === null) {
            /** @var Option<S> $none */
            $none = Option::none();

            return $none;
        }

        /** @var S $value */
        return self::some($value);
    }

    /**
     * @template S
     *
     * @param  S  $value
     * @return Option<S>
     */
    public static function some(mixed $value): self
    {
        return new self($value, true);
    }

    /**
     * @return Option<T>
     */
    public static function none(): self
    {
        return new self(null, false);
    }

    /**
     * @template S
     *
     * @param  callable(T): S  $mapper
     * @return S
     */
    public function map(callable $mapper): mixed
    {
        return $mapper($this->unwrap());
    }

    /**
     * @template S
     * @template R
     *
     * @param  callable(T): S  $mapper
     * @param  R  $default
     * @return S|R
     */
    public function mapOr(callable $mapper, mixed $default): mixed
    {
        if ($this->isSome()) {
            return $mapper($this->unwrap());
        }

        return $default;
    }

    /**
     * @template S
     *
     * @param  callable(T): S  $mapper
     * @return Option<S>
     */
    public function mapIntoOption(callable $mapper): Option
    {
        if ($this->isSome()) {
            $appliedValue = $mapper($this->unwrap());

            return Option::some($appliedValue);
        }

        // @phpstan-ignore-next-line
        return Option::none();
    }

    public function isNone(): bool
    {
        return ! $this->ok;
    }

    public function isSome(): bool
    {
        return $this->ok;
    }

    /**
     * @throws OptionUnwrapException
     *
     * @return T
     */
    public function unwrap(): mixed
    {
        if ($this->isSome()) {
            return $this->value;
        }
        throw new OptionUnwrapException();
    }

    /**
     * @template S
     *
     * @param  S  $default
     * @return T|S
     */
    public function unwrapOr(mixed $default): mixed
    {
        if ($this->isSome()) {
            return $this->value;
        }

        return $default;
    }

    /**
     * @template S
     *
     * @param  Option<S>  $other
     * @param  callable(T, S): bool  $comparator
     */
    public function equals(Option $other, callable $comparator): bool
    {
        if ($this->isNone() && $other->isNone()) {
            return true;
        }

        if ($this->isSome() && $other->isSome()) {
            return $comparator($this->unwrap(), $other->unwrap());
        }

        return false;
    }
}
