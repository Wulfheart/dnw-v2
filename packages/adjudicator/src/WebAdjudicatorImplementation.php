<?php

namespace Dnw\Adjudicator;

use Dnw\Adjudicator\Dto\AdjudicateGameRequest;
use Dnw\Adjudicator\Dto\AdjudicateGameResponse;
use Dnw\Adjudicator\Dto\DumbbotRequest;
use Dnw\Adjudicator\Dto\DumbbotResponse;
use Dnw\Adjudicator\Dto\VariantsResponse;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class WebAdjudicatorImplementation implements AdjudicatorService
{
    /**
     * @throws UnknownProperties
     * @throws RequestException
     */
    public function getVariants(): VariantsResponse
    {
        $response = Http::log()->baseUrl(config('diplomacy.adjudicator.base_url'))->get('variants');
        $response->throw();
        $dto = new VariantsResponse($response->json());
        $dto->json = $response->body();

        return $dto;
    }

    /**
     * @throws UnknownProperties
     * @throws RequestException
     */
    public function initializeGame(string $variant): AdjudicateGameResponse
    {
        $response = Http::log()->baseUrl(config('diplomacy.adjudicator.base_url'))->get(implode(
            '/',
            ['adjudicate', $variant]
        ));
        $response->throw();

        $dto = new AdjudicateGameResponse($response->json());
        $dto->json = $response->body();

        return $dto;
    }

    /**
     * @throws UnknownProperties
     * @throws RequestException
     */
    public function adjudicateGame(AdjudicateGameRequest $request): AdjudicateGameResponse
    {
        $response = Http::log()->baseUrl(config('diplomacy.adjudicator.base_url'))->post('adjudicate', $request->toArray());
        $response->throw();

        $dto = new AdjudicateGameResponse($response->json());
        $dto->json = $response->body();

        return $dto;
    }

    /**
     * @throws UnknownProperties
     * @throws RequestException
     */
    public function getDumbbotOrders(DumbbotRequest $request): DumbbotResponse
    {
        $response = Http::log()->baseUrl(config('diplomacy.adjudicator.base_url'))->post('dumbbot', $request->toArray());
        $response->throw();

        $dto = new DumbbotResponse($response->json());
        $dto->json = $response->body();

        return $dto;
    }
}
