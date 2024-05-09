<?php

namespace Dnw\Game\Core\Application\Command\CreateGame;

use Dnw\Foundation\Identity\Id;

final readonly class CreateGameCommand
{
    public function __construct(
        public Id $game_id,
        public string $name,
        public int $phase_length_in_minutes,
        public int $join_length_in_days,
        public bool $start_when_ready,
        public Id $variant_id,
        public bool $random_power_assignments,
        public ?Id $selected_power_id,
        public bool $is_ranked,
        public bool $is_anonymous,
        public bool $uses_custom_message_mode,
        public ?CustomMessageModePermissions $custom_message_mode_permissions,
        public ?string $message_mode_id,
        public ?int $number_of_supply_centers_to_win,
        public ?int $maximum_number_of_rounds,
        /** @var array<int> $weekdays_without_adjudication */
        public array $weekdays_without_adjudication,
        public Id $creator_id,
    ) {

    }
}
