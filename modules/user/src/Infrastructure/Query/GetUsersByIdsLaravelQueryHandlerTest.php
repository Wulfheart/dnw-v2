<?php

namespace Dnw\User\Infrastructure\Query;

use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\Identity\Id;
use Dnw\User\Application\Query\GetUsersByIds\GetUsersByIdsQuery;
use Dnw\User\Application\Query\GetUsersByIds\GetUsersByIdsQueryResult;
use Dnw\User\Application\Query\GetUsersByIds\UserData;
use Dnw\User\Infrastructure\UserModel;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\LaravelTestCase;

#[CoversClass(GetUsersByIdsLaravelQueryHandler::class)]

class GetUsersByIdsLaravelQueryHandlerTest extends LaravelTestCase
{
    public function test_no_results_in_error(): void
    {
        $query = new GetUsersByIdsQuery([Id::generate()]);
        $handler = new GetUsersByIdsLaravelQueryHandler();
        $result = $handler->handle($query);
        $this->assertTrue($result->isErr());
        $this->assertEquals(GetUsersByIdsQueryResult::E_USER_NOT_FOUND, $result->unwrapErr());
    }

    public function test_result_count_mismatch_results_in_error(): void
    {
        $firstUserId = Id::generate();
        $firstUsername = '::USER_1::';

        $secondUserId = Id::generate();
        $secondUsername = '::USER_2::';

        UserModel::factory()->name($firstUsername)->id($firstUserId)->create();
        UserModel::factory()->name($secondUsername)->id($secondUserId)->create();
        UserModel::factory()->create();

        $query = new GetUsersByIdsQuery([$firstUserId, $secondUserId, Id::generate()]);
        $handler = new GetUsersByIdsLaravelQueryHandler();

        $result = $handler->handle($query);

        $this->assertTrue($result->isErr());
        $this->assertEquals(GetUsersByIdsQueryResult::E_USER_NOT_FOUND, $result->unwrapErr());

    }

    public function test_maps_data(): void
    {
        $firstUserId = Id::generate();
        $firstUsername = '::USER_1::';

        $secondUserId = Id::generate();
        $secondUsername = '::USER_2::';

        UserModel::factory()->name($firstUsername)->id($firstUserId)->create();
        UserModel::factory()->name($secondUsername)->id($secondUserId)->create();
        UserModel::factory()->create();

        $query = new GetUsersByIdsQuery([$firstUserId, $secondUserId]);
        $handler = new GetUsersByIdsLaravelQueryHandler();

        $result = $handler->handle($query);

        $this->assertTrue($result->isOk());
        $this->assertEquals(
            ArrayCollection::fromArray([
                new UserData($firstUserId, $firstUsername),
                new UserData($secondUserId, $secondUsername),
            ]),
            $result->unwrap()
        );
    }
}
