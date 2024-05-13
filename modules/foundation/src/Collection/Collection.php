<?php

namespace Dnw\Foundation\Collection;

use ArrayIterator;
use IteratorAggregate;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use Traversable;

/**
 * @template T
 *
 * @implements IteratorAggregate<int, T>
 *
 * @phpstan-consistent-constructor
 */
abstract class Collection implements IteratorAggregate
{
    public function __construct(
        /** @var array<T> */
        private array $items = [],
    ) {
        $this->items = array_values($this->items);
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
        return $this->findBy($predicate)->isDefined();
    }

    /**
     * @template S
     *
     * @param  callable(T): S  $callback
     * @return Collection<S>
     */
    public function map(callable $callback): Collection
    {
        return new static(array_map($callback, $this->items));
    }

    /**
     * @param  callable(T): bool  $predicate
     * @return Option<T>
     */
    public function findBy(callable $predicate): Option
    {
        foreach ($this->items as $item) {
            if ($predicate($item)) {
                return Some::create($item);
            }
        }

        return None::create();
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @return array<T>
     */
    public function toArray(): array
    {
        return $this->items;
    }

    public function count(): int
    {
        return count($this->items);
    }
}
