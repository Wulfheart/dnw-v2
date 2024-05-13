<?php

namespace Dnw\Game\Core\Domain\Rule;

class GameRules
{
    public const string HAS_BEEN_STARTED = 'has_been_started';

    public const string HAS_NOT_BEEN_STARTED = 'has_not_been_started';

    public const string HAS_BEEN_FINISHED = 'has_been_finished';

    public const string HAS_NOT_BEEN_FINISHED = 'has_not_been_finished';

    public const string HAS_BEEN_ABANDONED = 'has_been_abandoned';

    public const string INITIAL_PHASE_DOES_NOT_EXIST = 'initial_phase_does_not_exist';

    public const string HAS_NO_AVAILABLE_POWERS = 'has_no_available_powers';

    public const string HAS_AVAILABLE_POWERS = 'has_available_powers';

    public const string PLAYER_ALREADY_JOINED = 'player_already_joined';

    public const string POWER_ALREADY_FILLED = 'power_already_filled';

    public const string PLAYER_NOT_IN_GAME = 'player_not_in_game';

    public const string GAME_NOT_MARKED_AS_READY_OR_JOIN_LENGTH_NOT_EXCEEDED
        = 'game_not_marked_as_ready_or_join_length_not_exceeded';

    public const string POWER_DOES_NOT_NEED_TO_SUBMIT_ORDERS
        = 'power_does_not_need_to_submit_orders';

    public const string PHASE_NOT_MARKED_AS_READY_AND_TIME_HAS_NOT_EXPIRED
        = 'phase_not_marked_as_ready_or_time_has_not_expired';

    public const string GAME_READY_FOR_ADJUDICATION = 'game_ready_for_adjudication';

    public const string PHASE_IS_ALREADY_SET = 'phase_is_already_set';

    public const string ORDERS_ALREADY_MARKED_AS_READY = 'orders_already_marked_as_ready';
}
