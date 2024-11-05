<?php

namespace Dnw\Game\Domain\Game\StateMachine;

class GameStates
{
    public const string CREATED = 'created';

    public const string PLAYERS_JOINING = 'players_joining';

    public const string ABANDONED = 'abandoned';

    public const string ORDER_SUBMISSION = 'order_submission';

    public const string ADJUDICATING = 'adjudicating';

    public const string FINISHED = 'finished';

    public const string NOT_ENOUGH_PLAYERS_BY_DEADLINE = 'not_enough_players_by_deadline';
}
