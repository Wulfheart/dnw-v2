<?php

namespace App\Web\Helper;

final readonly class Pagination
{
    public function __construct(
        private int $currentPage,
        private int $perPage,
        private int $totalCount,
    ) {
        if ($this->currentPage < 1) {
            throw PaginationException::currentPageIsLessThanOne($this->currentPage);
        }
        if ($this->perPage < 1) {
            throw PaginationException::perPageIsLessThanOne($this->perPage);
        }

        if ($this->totalCount < 0) {
            throw PaginationException::totalCountIsLessThanZero($this->totalCount);
        }
    }

    public function isOnLastPage(): bool
    {

        return $this->currentPage === $this->calculateLastPage();
    }

    private function calculateLastPage(): int
    {
        $lastPage = (int) ceil($this->totalCount / $this->perPage);
        if ($lastPage < 1) {
            return 1;
        }

        return $lastPage;
    }

    public function isOnFirstPage(): bool
    {
        return $this->currentPage === 1;
    }

    public function isOutOfRange(): bool
    {
        return $this->currentPage >= $this->calculateLastPage() + 1;
    }

    /**
     * @param  callable(int $currentPage): string  $urlResolver
     */
    public function viewModel(callable $urlResolver): PaginationViewModel
    {
        return new PaginationViewModel(
            $this->currentPage,
            $this->calculateLastPage(),
            $this->isOnFirstPage(),
            $this->isOnLastPage(),
            $urlResolver($this->currentPage + 1),
            $urlResolver($this->currentPage - 1),
            $urlResolver(1),
            $urlResolver($this->calculateLastPage())
        );
    }
}
