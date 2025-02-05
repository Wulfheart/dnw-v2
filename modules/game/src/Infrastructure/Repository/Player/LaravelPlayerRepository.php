<?php

namespace Dnw\Game\Infrastructure\Repository\Player;

use Dnw\Game\Domain\Game\StateMachine\GameStates;
use Dnw\Game\Domain\Player\Player;
use Dnw\Game\Domain\Player\Repository\Player\LoadPlayerResult;
use Dnw\Game\Domain\Player\Repository\Player\PlayerRepositoryInterface;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use Dnw\Game\Infrastructure\Model\Game\PowerModel;
use Illuminate\Database\Eloquent\Builder;

class LaravelPlayerRepository implements PlayerRepositoryInterface
{
    public function load(PlayerId $playerId): LoadPlayerResult
    {
        $count = PowerModel::with('game')->whereDoesntHave('game', function (Builder $query) {
            $query->where('current_state', GameStates::FINISHED);
        })->where('player_id', (string) $playerId)->count();

        return LoadPlayerResult::ok(new Player(
            $playerId,
            $count
        ));
    }
}
