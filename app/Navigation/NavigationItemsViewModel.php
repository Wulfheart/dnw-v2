<?php

namespace App\Navigation;

use ArrayIterator;
use Dnw\Foundation\ViewModel\ViewModel;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<NavigationItemViewModel>
 */
class NavigationItemsViewModel extends ViewModel implements IteratorAggregate
{
    public function __construct(
        /** @var array<NavigationItemViewModel> $items */
        public array $items
    ) {

    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}
