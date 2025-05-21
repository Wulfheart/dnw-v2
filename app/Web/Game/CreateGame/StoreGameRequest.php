<?php

namespace App\Web\Game\CreateGame;

use Dnw\Foundation\Identity\IdRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{
    public const string KEY_NAME = 'name';

    public const string PHASE_LENGTH_IN_MINUTES = 'phaseLengthInMinutes';

    public const string KEY_JOIN_LENGTH_IN_DAYS = 'joinLengthInDays';

    public const string KEY_START_WHEN_READY = 'startWhenReady';

    public const string KEY_VARIANT_ID = 'variantId';

    public const string KEY_RANDOM_POWER_ASSIGNMENTS = 'randomPowerAssignments';

    public const string KEY_SELECTED_VARIANT_POWER_ID = 'selectedVariantPowerId';

    public const string KEY_IS_RANKED = 'isRanked';

    public const string KEY_IS_ANONYMOUS = 'isAnonymous';

    public const string KEY_WEEKDAYS_WITHOUT_ADJUDICATION = 'weekdaysWithoutAdjudication';

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            self::KEY_NAME => ['required', 'string'],
            self::PHASE_LENGTH_IN_MINUTES => ['required', 'integer', 'min:10', 'max:1440'],
            self::KEY_JOIN_LENGTH_IN_DAYS => ['required', 'integer', 'min:1', 'max:365'],
            self::KEY_START_WHEN_READY => ['required', 'boolean'],
            self::KEY_VARIANT_ID => ['required', 'string'],
            // self::KEY_RANDOM_POWER_ASSIGNMENTS => ['required', 'boolean'],
            // self::KEY_SELECTED_VARIANT_POWER_ID => ['required_if_accepted:randomPowerAssignments', IdRule::class],
            // self::KEY_IS_RANKED => ['required', 'boolean'],
            // self::KEY_IS_ANONYMOUS => ['required', 'boolean'],
            // self::KEY_WEEKDAYS_WITHOUT_ADJUDICATION => ['required', 'array', 'max:6'],
            // self::KEY_WEEKDAYS_WITHOUT_ADJUDICATION . '.*' => ['integer', 'min:0', 'max:6'],
        ];
    }
}
