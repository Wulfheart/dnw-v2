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

class TestWithCachingAdjudicatorImplementation implements AdjudicatorService
{
    /**
     * @throws UnknownProperties
     * @throws RequestException
     */
    public function getVariants(): VariantsResponse
    {
        $response = Http::baseUrl(config('diplomacy.adjudicator.base_url'))->get('variants');
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
        $json_raw = $this->do($variant, function ($data) {
            $response = Http::baseUrl(config('diplomacy.adjudicator.base_url'))->get(implode(
                '/',
                ['adjudicate', $data]
            ));
            $response->throw();

            return $response->body();
        });

        $dto = new AdjudicateGameResponse(json_decode($json_raw, true));
        $dto->json = $json_raw;

        return $dto;
    }

    /**
     * @throws UnknownProperties
     * @throws RequestException
     */
    public function adjudicateGame(AdjudicateGameRequest $request): AdjudicateGameResponse
    {
        $json_raw = $this->do($request, function ($data) {
            $response = Http::baseUrl(config('diplomacy.adjudicator.base_url'))->post('adjudicate', $data->toArray());
            $response->throw();

            return $response->body();
        });

        $dto = new AdjudicateGameResponse(json_decode($json_raw, true));
        $dto->json = $json_raw;

        return $dto;
    }

    /**
     * @throws UnknownProperties
     * @throws RequestException
     */
    public function getDumbbotOrders(DumbbotRequest $request): DumbbotResponse
    {
        $response = Http::baseUrl(config('diplomacy.adjudicator.base_url'))->post('dumbbot', $request->toArray());
        $response->throw();

        $dto = new DumbbotResponse($response->json());
        $dto->json = $response->body();

        return $dto;
    }

    protected function do(mixed $data, callable $callback): string
    {
        $hash = hash('sha256', json_encode($data));
        $location = base_path('tests/cache/' . $hash . '.response.json');

        if (file_exists($location)) {
            $json_raw = file_get_contents($location);
        } else {
            $json_raw = $callback($data);
            file_put_contents($location, $json_raw);
        }

        return $json_raw;
    }
}
