<?php

namespace App\Web\Game\ListNewGames;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

final class ListNewGamesRequest extends FormRequest
{
    public const KEY_PAGE = 'page';

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => ['integer', 'min:1'],
        ];
    }

    public function page(): int
    {
        return $this->integer(self::KEY_PAGE, 1);
    }
}
