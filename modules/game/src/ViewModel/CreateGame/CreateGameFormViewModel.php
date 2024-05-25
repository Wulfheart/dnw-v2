<?php

namespace Dnw\Game\ViewModel\CreateGame;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Dnw\Foundation\ViewModel\Option;
use Dnw\Foundation\ViewModel\Options;
use Dnw\Foundation\ViewModel\ViewModel;
use Dnw\Game\Core\Application\Query\GetAllVariants\VariantDto;
use Dnw\Game\ViewModel\CreateGame\ViewModel\VariantInformationOption;
use Dnw\Game\ViewModel\CreateGame\ViewModel\VariantInformationOptions;

class CreateGameFormViewModel extends ViewModel
{
    public function __construct(
        public string $create_game_title,
        public string $create_game_description,
        public string $create_game_url,
        public string $advanced_settings_title,
        public string $name_label,
        public string $phase_length_in_minutes_label,
        public string $phase_length_in_minutes_description,
        public Options $phase_length_in_minutes_options,
        public string $variant_id_label,
        public VariantInformationOptions $variant_id_options,
        public string $join_length_in_days_label,
        public int $join_length_in_days_default_value,
        public Options $start_when_ready_options,
        public string $no_adjudication_weekdays_label,
        public string $no_adjudication_weekdays_description,
        public Options $no_adjudication_weekdays_options,
        public string $anonymous_orders_label,
        public string $random_power_assignments_label,
        public string $selected_power_id_label,
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

        $phaseLengthInMinutesOptions = new Options(
            array_map(
                fn (int $length) => new Option(
                    (string) $length,
                    $differFunction($length),
                    $length == CarbonInterface::HOURS_PER_DAY * CarbonInterface::MINUTES_PER_HOUR
                ),
                $allowedLengths
            )
        );

        $variantInformationOptions = new VariantInformationOptions(
            array_map(
                fn (VariantDto $variantDto) => new VariantInformationOption(
                    (string) $variantDto->id,
                    $variantDto->name,
                    $variantDto->name === 'Standard',
                    $variantDto->description,
                    new Options(
                        array_map(
                            fn ($power) => new Option(
                                (string) $power->variantPowerId,
                                $power->name,
                                false
                            ),
                            $variantDto->powers
                        )
                    ),
                ),
                $variants
            ),
        );

        $noAdjudicationWeekdaysOptions = new Options([
            new Option('1', 'Montag', false),
            new Option('2', 'Dienstag', false),
            new Option('3', 'Mittwoch', false),
            new Option('4', 'Donnerstag', false),
            new Option('5', 'Freitag', false),
            new Option('6', 'Samstag', false),
            new Option('0', 'Sonntag', false),
        ]);

        $startWhenReadyOptions = new Options([
            new Option('1', 'Das Spiel wird gestartet, sobald genug Spieler beigetreten sind.', true),
            new Option('0', 'Das Spiel startet erst, wenn das Start-Datum und die Start-Zeit erreicht ist.', false),
        ]);

        return new self(
            'Neues Spiel erstellen',
            ' Beginne ein neues Spiel; du entscheidest, wie es heißt, wie lange die Phasen dauern, und was es wert ist.',
            route('game.store'),
            'Erweiterte Einstellungen',
            'Name',
            'Phasenlänge',
            'Wie lange dauert jede Phase?',
            $phaseLengthInMinutesOptions,
            'Variante',
            $variantInformationOptions,
            'Länge der Beitrittsphase in Tagen',
            10,
            $startWhenReadyOptions,
            'Keine Spielauswertung an',
            'Wähle die Wochentage aus, an denen keine Spielauswertung stattfinden soll.',
            $noAdjudicationWeekdaysOptions,
            'Anonyme Befehle',
            'Zufällige Mächtezuweisungen',
            'Ausgewählte Macht',
            'Rangliste',
            'Spiel erstellen',
        );
    }
}
