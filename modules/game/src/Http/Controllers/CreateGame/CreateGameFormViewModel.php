<?php

namespace Dnw\Game\Http\Controllers\CreateGame;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Dnw\Foundation\ViewModel\Option;
use Dnw\Foundation\ViewModel\ViewModel;
use Dnw\Game\Core\Application\Query\GetAllVariants\VariantDto;
use Dnw\Game\Http\Controllers\CreateGame\ViewModel\VariantInformationOption;

class CreateGameFormViewModel extends ViewModel
{
    public function __construct(
        public string $create_game_title,
        public string $create_game_description,
        public string $create_game_url,
        public string $advanced_settings_title,
        public string $name_form_key,
        public string $name_label,
        public string $phase_length_in_minutes_form_key,
        public string $phase_length_in_minutes_label,
        /** @var array<Option> $phase_length_in_minutes_options */
        public array $phase_length_in_minutes_options,
        public string $variant_id_form_key,
        public string $variant_id_label,
        /** @var array<VariantInformationOption> $variant_id_options */
        public array $variant_id_options,
        public string $join_length_in_days_form_key,
        public string $join_length_in_days_label,
        public int $join_length_in_days_default_value,
        public string $start_when_ready_form_key,
        /** @var array<Option> $start_when_ready_options */
        public array $start_when_ready_options,
        public string $anonymous_orders_form_key,
        public string $anonymous_orders_label,
        public string $random_power_assignments_form_key,
        public string $random_power_assignments_label,
        public string $selected_power_id_form_key,
        public string $selected_power_id_label,
        public string $is_ranked_form_key,
        public string $is_ranked_label,
        public string $submit_button_label,
    ) {

    }

    /**
     * @param  array<VariantDto>  $variants
     */
    public static function fromLaravel(
        array $variants,
    ): self {
        $allowedLengths = [5, 10, 15, 20, 30, 60, 120, 240, 360, 480, 600, 720, 840, 960, 1080, 1200, 1320, 1440, 2160, 2880, 4320, 5760, 7200, 8640, 10080, 14400];

        $differFunction = function (int $minutes) {
            /** @var CarbonImmutable $baseDate */
            $baseDate = CarbonImmutable::create(2021, 1, 1, 0, 0, 0);
            $date = $baseDate->addMinutes($minutes);

            return $date->diffAsCarbonInterval($baseDate)->forHumans();
        };

        $phaseLengthInMinutesOptions = array_map(
            fn (int $length) => new Option(
                (string) $length,
                $differFunction($length),
                $length == CarbonInterface::HOURS_PER_DAY * CarbonInterface::MINUTES_PER_HOUR
            ),
            $allowedLengths
        );

        $variantInformationOptions = array_map(
            fn (VariantDto $variantDto) => new VariantInformationOption(
                (string) $variantDto->id,
                $variantDto->name,
                $variantDto->name === 'Standard',
                $variantDto->description,
                array_map(
                    fn ($power) => new Option(
                        (string) $power->variantPowerId,
                        $power->name,
                        false
                    ),
                    $variantDto->powers
                ),
            ),
            $variants
        );

        $startWhenReadyOptions = [
            new Option('1', 'Das Spiel wird gestartet, sobald genug Spieler beigetreten sind.', true),
            new Option('0', 'Das Spiel startet erst, wenn das Start-Datum und die Start-Zeit erreicht ist.', false),
        ];

        return new self(
            'Neues Spiel erstellen',
            ' Beginne ein neues Spiel; du entscheidest, wie es heißt, wie lange die Phasen dauern, und was es wert ist.',
            route('game.store'),
            'Erweiterte Einstellungen',
            CreateGameRequest::KEY_NAME,
            'Name',
            CreateGameRequest::KEY_PHASE_LENGTH_IN_MINUTES,
            'Phasenlänge',
            $phaseLengthInMinutesOptions,
            CreateGameRequest::KEY_VARIANT_ID,
            'Variante',
            $variantInformationOptions,
            CreateGameRequest::KEY_JOIN_LENGTH_IN_DAYS,
            'Länge der Beitrittsphase in Tagen',
            10,
            CreateGameRequest::KEY_START_WHEN_READY,
            $startWhenReadyOptions,
            CreateGameRequest::KEY_IS_ANONYMOUS,
            'Anonyme Befehle',
            CreateGameRequest::KEY_RANDOM_POWER_ASSIGNMENTS,
            'Zufällige Mächtezuweisungen',
            CreateGameRequest::KEY_SELECTED_POWER_ID,
            'Ausgewählte Macht',
            CreateGameRequest::KEY_IS_RANKED,
            'Rangliste',
            'Spiel erstellen',
        );
    }
}
