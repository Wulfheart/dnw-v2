<?php

namespace App\Web\Helper;

use Exception;

final class PaginationException extends Exception
{
    public const int CURRENT_PAGE_MUST_NOT_BE_LESS_THAN_ONE = 1;

    public const int PER_PAGE_MUST_NOT_BE_LESS_THAN_ONE = 2;

    public const int TOTAL_COUNT_MUST_NOT_BE_LESS_THAN_ZERO = 3;

    public static function currentPageIsLessThanOne(int $currentPage): self
    {
        return new self("Current Page ($currentPage) must not be less than one.", self::CURRENT_PAGE_MUST_NOT_BE_LESS_THAN_ONE);
    }

    public static function perPageIsLessThanOne(int $perPage): self
    {
        return new self("Per Page ($perPage) must not be less than one.", self::PER_PAGE_MUST_NOT_BE_LESS_THAN_ONE);
    }

    public static function totalCountIsLessThanZero(int $totalCount): self
    {
        return new self("Total Count ($totalCount) must not be less than zero.", self::TOTAL_COUNT_MUST_NOT_BE_LESS_THAN_ZERO);
    }
}
