<?php

namespace App\Web\Helper;

final readonly class PaginationViewModel
{
    public function __construct(
        public int $currentPage,
        public int $lastPage,
        public bool $isOnFirstPage,
        public bool $isOnLastPage,
        public string $nextPageUrl,
        public string $previousPageUrl,
        public string $firstPageUrl,
        public string $lastPageUrl,
    ) {}
}
