<?php

namespace App\Foundation\Id;

use Dnw\Foundation\Identity\Id;
use Exception;

final class FakeIdGenerator implements IdGeneratorInterface
{
    private int $currentIndex = 0;

    /**
     * @param  list<Id>  $ids
     */
    public function __construct(
        private array $ids = []
    ) {}

    public function generate(): Id
    {
        if ($this->currentIndex >= count($this->ids)) {
            throw new Exception('No more ids');
        }
        $id = $this->ids[$this->currentIndex];
        $this->currentIndex++;

        return $id;
    }

    public function addId(Id $id): void
    {
        $this->ids[] = $id;
    }
}
