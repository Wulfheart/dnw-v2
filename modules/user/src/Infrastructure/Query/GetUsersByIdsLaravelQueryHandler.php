<?php

namespace Dnw\User\Infrastructure\Query;

use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\Identity\Id;
use Dnw\User\Application\Query\GetUsersByIds\GetUsersByIdsQuery;
use Dnw\User\Application\Query\GetUsersByIds\GetUsersByIdsQueryHandlerInterface;
use Dnw\User\Application\Query\GetUsersByIds\GetUsersByIdsQueryResult;
use Dnw\User\Application\Query\GetUsersByIds\UserData;
use Dnw\User\Infrastructure\UserModel;

final class GetUsersByIdsLaravelQueryHandler implements GetUsersByIdsQueryHandlerInterface
{
    public function handle(GetUsersByIdsQuery $query): GetUsersByIdsQueryResult
    {
        $users = UserModel::whereIn('id', $query->ids)->select(['id', 'name'])->get();
        $count = $users->count();
        $expectedCount = count($query->ids);
        if ($count !== $expectedCount) {
            return GetUsersByIdsQueryResult::err(GetUsersByIdsQueryResult::E_USER_NOT_FOUND);
        }

        $userData = $users->map(function (UserModel $user) {
            return new UserData(Id::fromString($user->id), $user->name);
        })->toArray();

        return GetUsersByIdsQueryResult::ok(ArrayCollection::fromArray($userData));
    }
}
