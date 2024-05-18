<?php

namespace Dnw\Game\Http\Controllers\CreateGame;

use Dnw\Foundation\Identity\IdRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateGameRequest extends FormRequest
{
    public const string KEY_NAME = 'name';
    public const string KEY_PHASE_LENGTH_IN_MINUTES = 'phase_length_in_minutes';
    public const string KEY_JOIN_LENGTH_IN_DAYS = 'join_length_in_days';
    public const string KEY_START_WHEN_READY = 'start_when_ready';
    public const string KEY_VARIANT_ID = 'variant_id';
    public const string KEY_RANDOM_POWER_ASSIGNMENTS = 'random_power_assignments';
    public const string KEY_SELECTED_POWER_ID = 'selected_power_id';
    public const string KEY_IS_RANKED = 'is_ranked';
    public const string KEY_IS_ANONYMOUS = 'is_anonymous';
    public const string KEY_WEEKDAYS_WITHOUT_ADJUDICATION = 'weekdays_without_adjudication';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            self::KEY_NAME => ['required', 'string'],
            self::KEY_PHASE_LENGTH_IN_MINUTES => ['required', 'integer', 'gt:4'],
            self::KEY_JOIN_LENGTH_IN_DAYS => ['required', 'integer', 'gt:0', 'lt:365'],
            self::KEY_START_WHEN_READY => ['required', 'boolean'],
            self::KEY_VARIANT_ID => ['required', new IdRule],
            self::KEY_RANDOM_POWER_ASSIGNMENTS => ['required', 'boolean'],
            self::KEY_SELECTED_POWER_ID => [new IdRule],
            self::KEY_IS_RANKED => ['required', 'boolean'],
            self::KEY_IS_ANONYMOUS => ['required', 'boolean'],
            self::KEY_WEEKDAYS_WITHOUT_ADJUDICATION => ['array'],
            self::KEY_WEEKDAYS_WITHOUT_ADJUDICATION . '.*' => ['integer', 'between:0,6'],
        ];
    }
}
