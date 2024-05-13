<?php

namespace Dnw\Foundation\Collection;

/**
 * @template U
 *
 * @extends Collection<U>
 */
class ArrayCollection extends Collection
{
    /**
     * @param  array<U>  $data
     * @return self<U>
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }
}
