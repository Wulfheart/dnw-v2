<?php

namespace App\Web\Helper;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Pagination::class)]
#[CoversClass(PaginationViewModel::class)]
#[CoversClass(PaginationException::class)]
class PaginationTest extends TestCase
{
    public function test_current_page_must_not_be_less_than_one(): void
    {
        $this->expectException(PaginationException::class);
        $this->expectExceptionCode(PaginationException::CURRENT_PAGE_MUST_NOT_BE_LESS_THAN_ONE);
        new Pagination(0, 1, 1);
    }

    public function test_per_page_must_not_be_less_than_one(): void
    {
        $this->expectException(PaginationException::class);
        $this->expectExceptionCode(PaginationException::PER_PAGE_MUST_NOT_BE_LESS_THAN_ONE);
        new Pagination(1, 0, 1);
    }

    public function test_total_count_must_not_be_less_than_zero(): void
    {
        $this->expectException(PaginationException::class);
        $this->expectExceptionCode(PaginationException::TOTAL_COUNT_MUST_NOT_BE_LESS_THAN_ZERO);

        new Pagination(1, 1, -1);
    }

    public function test_isOnLastPage(): void
    {
        $pagination = new Pagination(1, 1, 0);
        $this->assertTrue($pagination->isOnLastPage());

        $pagination = new Pagination(1, 1, 1);
        $this->assertTrue($pagination->isOnLastPage());

        $pagination = new Pagination(1, 10, 11);
        $this->assertFalse($pagination->isOnLastPage());

        $pagination = new Pagination(1, 10, 21);
        $this->assertFalse($pagination->isOnLastPage());

        $pagination = new Pagination(2, 10, 20);
        $this->assertTrue($pagination->isOnLastPage());
    }

    public function test_isOnFirstPage(): void
    {
        $pagination = new Pagination(1, 1, 1);
        $this->assertTrue($pagination->isOnFirstPage());

        $pagination = new Pagination(2, 1, 10);
        $this->assertFalse($pagination->isOnFirstPage());
    }

    public function test_isOutOfRange(): void
    {
        $pagination = new Pagination(1, 1, 1);
        $this->assertFalse($pagination->isOutOfRange());

        $pagination = new Pagination(1, 1, 0);
        $this->assertFalse($pagination->isOutOfRange());

        $pagination = new Pagination(2, 1, 1);
        $this->assertTrue($pagination->isOutOfRange());
    }

    public function test_viewModel(): void
    {
        $pagination = new Pagination(2, 10, 30);
        $viewModel = $pagination->viewModel(fn (int $page) => (string) $page);

        $this->assertEquals(2, $viewModel->currentPage);
        $this->assertEquals(3, $viewModel->lastPage);
        $this->assertFalse($viewModel->isOnFirstPage);
        $this->assertFalse($viewModel->isOnLastPage);

        $this->assertEquals('3', $viewModel->nextPageUrl);
        $this->assertEquals('1', $viewModel->previousPageUrl);
        $this->assertEquals('1', $viewModel->firstPageUrl);
        $this->assertEquals('3', $viewModel->lastPageUrl);

    }
}
