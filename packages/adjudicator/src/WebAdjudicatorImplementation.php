<?php

namespace Dnw\Adjudicator;

use Dnw\Adjudicator\Dto\AdjudicateGameRequestDto;
use Dnw\Adjudicator\Dto\AdjudicateGameResponseDto;
use Dnw\Adjudicator\Dto\DumbbotRequestDto;
use Dnw\Adjudicator\Dto\DumbbotResponseDto;
use Dnw\Adjudicator\Dto\VariantsResponseDto;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class WebAdjudicatorImplementation implements AdjudicatorService
{
    /**
     * @throws UnknownProperties
     * @throws RequestException
     */
    public function getVariants(): VariantsResponseDto
    {
        $response = Http::log()->baseUrl(config('diplomacy.adjudicator.base_url'))->get('variants');
        $response->throw();
        $dto = new VariantsResponseDto($response->json());
        $dto->json = $response->body();

        return $dto;
    }

    /**
     * @throws UnknownProperties
     * @throws RequestException
     */
    public function initializeGame(string $variant): AdjudicateGameResponseDto
    {
        $response = Http::log()->baseUrl(config('diplomacy.adjudicator.base_url'))->get(implode('/',
            ['adjudicate', $variant]));
        $response->throw();

        $dto = new AdjudicateGameResponseDto($response->json());
        $dto->json = $response->body();

        return $dto;
    }

    /**
     * @throws UnknownProperties
     * @throws RequestException
     */
    public function adjudicateGame(AdjudicateGameRequestDto $request): AdjudicateGameResponseDto
    {
        $response = Http::log()->baseUrl(config('diplomacy.adjudicator.base_url'))->post('adjudicate', $request->toArray());
        $response->throw();

        $dto = new AdjudicateGameResponseDto($response->json());
        $dto->json = $response->body();

        return $dto;
    }

    /**
     * @throws UnknownProperties
     * @throws RequestException
     */
    public function getDumbbotOrders(DumbbotRequestDto $request): DumbbotResponseDto
    {
        $response = Http::log()->baseUrl(config('diplomacy.adjudicator.base_url'))->post('dumbbot', $request->toArray());
        $response->throw();

        $dto = new DumbbotResponseDto($response->json());
        $dto->json = $response->body();

        return $dto;
    }
}
