<?php

namespace App\Web\Game\CreateGame;

use App\Web\Form\Fields\Heading;
use App\Web\Form\Fields\NumberInput;
use App\Web\Form\Fields\Select;
use App\Web\Form\Fields\SelectOption;
use App\Web\Form\Fields\Separator;
use App\Web\Form\Fields\TextInput;
use App\Web\Form\Form;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Dnw\Game\Core\Application\Query\GetAllVariants\VariantDto;

class CreateGameFormViewModel
{
    public function __construct(
        public string $create_game_title,
        public string $create_game_description,
        public Form $form,
    ) {}

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
            fn (int $length) => new SelectOption(
                (string) $length,
                $differFunction($length),
                $length == CarbonInterface::HOURS_PER_DAY * CarbonInterface::MINUTES_PER_HOUR
            ),
            $allowedLengths
        );

        $variantInformationOptions = array_map(
            fn (VariantDto $variantDto) => new SelectOption(
                (string) $variantDto->id,
                $variantDto->name,
                $variantDto->name === 'Standard',
            ),
            $variants
        );

        $noAdjudicationWeekdaysOptions = [
            new SelectOption('1', 'Montag', false),
            new SelectOption('2', 'Dienstag', false),
            new SelectOption('3', 'Mittwoch', false),
            new SelectOption('4', 'Donnerstag', false),
            new SelectOption('5', 'Freitag', false),
            new SelectOption('6', 'Samstag', false),
            new SelectOption('0', 'Sonntag', false),
        ];

        $startWhenReadyOptions = [
            new SelectOption('1', 'Das Spiel wird gestartet, sobald genug Spieler beigetreten sind.', true),
            new SelectOption('0', 'Das Spiel startet erst, wenn das Start-Datum und die Start-Zeit erreicht ist.', false),
        ];

        $form = new Form(
            route('game.store'),
            'Spiel erstellen',
            fields: [
                new TextInput(StoreGameRequest::KEY_NAME, 'Name'),
                new Select(
                    StoreGameRequest::PHASE_LENGTH_IN_MINUTES,
                    'Phasenlänge',
                    'Wie lange dauert jede Phase?',
                    $phaseLengthInMinutesOptions
                ),
                new Separator(),
                new Heading('Erweiterte Einstellungen'),
                new Select(
                    StoreGameRequest::KEY_VARIANT_ID,
                    'Variante',
                    options: $variantInformationOptions
                ),
                new NumberInput(StoreGameRequest::KEY_JOIN_LENGTH_IN_DAYS, 'Länge der Beitrittsphase in Tagen', defaultValue: 10, min: 1, max: 365),
                new Select(
                    StoreGameRequest::KEY_START_WHEN_READY,
                    'Spielstart',
                    'Wann soll das Spiel beginnen?',
                    $startWhenReadyOptions
                ),
            ],
        );

        return new self(
            'Neues Spiel erstellen',
            ' Beginne ein neues Spiel; du entscheidest, wie es heißt, wie lange die Phasen dauern, und was es wert ist.',
            $form,
        );
    }
}
