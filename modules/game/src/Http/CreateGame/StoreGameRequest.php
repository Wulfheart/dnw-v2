<?php

namespace Dnw\Game\Http\CreateGame;

use Dnw\Foundation\Identity\IdRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreGameRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'phaseLengthInMinutes' => ['required', 'integer', 'min:10', 'max:1440'],
            'joinLengthInDays' => ['required', 'integer', 'min:1', 'max:365'],
            'startWhenReady' => ['required', 'boolean'],
            'variantId' => ['required', IdRule::class],
            'randomPowerAssignments' => ['required', 'boolean'],
            'selectedVariantPowerId' => [IdRule::class, 'required_if_accepted:randomPowerAssignments'],
            'isRanked' => ['required', 'boolean'],
            'isAnonymous' => ['required', 'boolean'],
            'weekdaysWithoutAdjudication' => ['required', 'array', 'max:6'],
            'weekdaysWithoutAdjudication.*' => ['integer', 'min:0', 'max:6'],
        ];
    }
}
