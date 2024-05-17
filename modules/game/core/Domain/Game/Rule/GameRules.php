<?php

namespace Dnw\Game\Core\Domain\Game\Rule;

class GameRules
{
    public const string EXPECTS_STATE_CREATED = 'expects_state_created';

    public const string EXPECTS_STATE_PLAYERS_JOINING = 'expects_state_players_joining';

    public const string EXPECTS_STATE_ADJUDICATING = 'expects_state_adjudicating';

    public const string EXPECTS_STATE_ORDER_SUBMISSION = 'expects_state_order_submission';

    public const string JOIN_LENGTH_IS_EXCEEDED = 'join_length_is_exceeded';

    public const string PLAYER_ALREADY_JOINED = 'player_already_joined';

    public const string POWER_ALREADY_FILLED = 'power_already_filled';

    public const string PLAYER_NOT_IN_GAME = 'player_not_in_game';

    public const string POWER_DOES_NOT_NEED_TO_SUBMIT_ORDERS
        = 'power_does_not_need_to_submit_orders';

    public const string GAME_PHASE_TIME_EXCEEDED = 'game_phase_time_exceeded';

    public const string ORDERS_ALREADY_MARKED_AS_READY = 'orders_already_marked_as_ready';
}
