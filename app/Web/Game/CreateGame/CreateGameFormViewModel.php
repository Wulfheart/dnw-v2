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
use Dnw\Game\Application\Query\GetAllVariants\VariantDto;

class CreateGameFormViewModel
{
    public function __construct(
        public string $create_game_title,
        public string $create_game_description,
        public bool $canParticipateInAnotherGame,
        public Form $form,
    ) {}

    /**
     * @param  array<VariantDto>  $variants
     */
    public static function fromLaravel(
        array $variants,
        bool $canParticipateInAnotherGame,
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
                (string) $variantDto->key,
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
            new SelectOption('1', __('game.new.start_when_ready_true_option'), true),
            new SelectOption('0', __('game.new.start_when_ready_false_option'), false),
        ];

        $form = new Form(
            route('game.store'),
            __('game.new.action'),
            fields: [
                new TextInput(StoreGameRequest::KEY_NAME, __('game.new.name')),
                new Select(
                    StoreGameRequest::PHASE_LENGTH_IN_MINUTES,
                    __('game.new.phase_length'),
                    'Wie lange dauert jede Phase?',
                    $phaseLengthInMinutesOptions
                ),
                new Separator(),
                new Heading(__('game.new.advanced_settings')),
                new Select(
                    StoreGameRequest::KEY_VARIANT_ID,
                    __('game.new.variant'),
                    options: $variantInformationOptions
                ),
                new NumberInput(StoreGameRequest::KEY_JOIN_LENGTH_IN_DAYS, __('game.new.join_length_in_days'), defaultValue: 10, min: 1, max: 365),
                new Select(
                    StoreGameRequest::KEY_START_WHEN_READY,
                    __('game.new.start_mode'),
                    'Wann soll das Spiel beginnen?',
                    $startWhenReadyOptions
                ),
            ],
        );

        return new self(
            __('game.new.title'),
            __('game.new.description'),
            $canParticipateInAnotherGame,
            $form,
        );
    }
}
