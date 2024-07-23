<?php

namespace Dnw\Game\Core\Application\Query\GetGame\Dto;

enum GameStateEnum: string
{
    case CREATED = 'created';

    case PLAYERS_JOINING = 'players_joining';

    case ABANDONED = 'abandoned';

    case ORDER_SUBMISSION = 'order_submission';

    case ADJUDICATING = 'adjudicating';

    case FINISHED = 'finished';

    case NOT_ENOUGH_PLAYERS_BY_DEADLINE = 'not_enough_players_by_deadline';

    public static function fromGameState(string $gameState): self
    {
        return self::from($gameState);
    }
}
