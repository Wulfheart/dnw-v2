<?php

namespace Dnw\Foundation\Collection;

use ArrayIterator;
use Dnw\Foundation\Exception\NotFoundException;
use IteratorAggregate;
use Traversable;
use Wulfheart\Option\Option;

/**
 * @template T
 *
 * @implements IteratorAggregate<int, T>
 *
 * @phpstan-consistent-constructor
 */
abstract class Collection implements IteratorAggregate
{
    /** @var list<T> */
    private array $items = [];

    /**
     * @param  array<T>  $items
     */
    public function __construct(
        array $items = [],
    ) {
        $this->items = array_values($items);
    }

    public static function empty(): static
    {
        return new static();
    }

    /**
     * @param  T  ...$items
     */
    public static function build(...$items): static
    {
        return new static($items);
    }

    /**
     * @param  T  $item
     */
    public function push(mixed $item): void
    {
        $this->items[] = $item;
    }

    /**
     * @param  callable(T): bool  $predicate
     * @return Collection<T>
     */
    public function filter(callable $predicate): Collection
    {
        return new static(array_filter($this->items, $predicate));
    }

    /**
     * @param  callable(T): bool  $predicate
     */
    public function contains(callable $predicate): bool
    {
        return $this->findBy($predicate)->isSome();
    }

    /**
     * @template S
     *
     * @param  callable(T): S  $callback
     * @return Collection<S>
     */
    public function map(callable $callback): Collection
    {
        return new ArrayCollection(array_map($callback, $this->items));
    }

    /**
     * @param  callable(T): bool  $predicate
     * @return Option<T>
     */
    public function findBy(callable $predicate): Option
    {
        foreach ($this->items as $item) {
            if ($predicate($item)) {
                return Option::some($item);
            }
        }

        return Option::none();
    }

    /**
     * @param  callable(T): bool  $predicate
     */
    public function every(callable $predicate): bool
    {
        foreach ($this->items as $item) {
            if (! $predicate($item)) {
                return false;
            }
        }

        return true;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @template U
     *
     * @param  Collection<U>  $c
     */
    public static function fromCollection(Collection $c): static
    {
        return new static($c->toArray());
    }

    /**
     * @return list<T>
     */
    public function toArray(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @throws NotFoundException
     *
     * @return T
     */
    public function first(): mixed
    {
        return $this->items[0] ?? throw new NotFoundException();
    }

    /**
     * @param  int<0,max>  $offset
     *
     * @throws NotFoundException
     *
     * @return T
     */
    public function getOffset(int $offset): mixed
    {
        return $this->items[$offset] ?? throw new NotFoundException();
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }
}
