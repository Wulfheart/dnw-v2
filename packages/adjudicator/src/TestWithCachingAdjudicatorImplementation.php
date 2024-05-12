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

class TestWithCachingAdjudicatorImplementation implements AdjudicatorService
{
    /**
     * @throws UnknownProperties
     * @throws RequestException
     */
    public function getVariants(): VariantsResponseDto
    {
        $response = Http::baseUrl(config('diplomacy.adjudicator.base_url'))->get('variants');
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
        $json_raw = $this->do($variant, function ($data) {
            $response = Http::baseUrl(config('diplomacy.adjudicator.base_url'))->get(implode('/',
                ['adjudicate', $data]));
            $response->throw();

            return $response->body();
        });

        $dto = new AdjudicateGameResponseDto(json_decode($json_raw, true));
        $dto->json = $json_raw;

        return $dto;
    }

    /**
     * @throws UnknownProperties
     * @throws RequestException
     */
    public function adjudicateGame(AdjudicateGameRequestDto $request): AdjudicateGameResponseDto
    {
        $json_raw = $this->do($request, function ($data) {
            $response = Http::baseUrl(config('diplomacy.adjudicator.base_url'))->post('adjudicate', $data->toArray());
            $response->throw();

            return $response->body();
        });

        $dto = new AdjudicateGameResponseDto(json_decode($json_raw, true));
        $dto->json = $json_raw;

        return $dto;
    }

    /**
     * @throws UnknownProperties
     * @throws RequestException
     */
    public function getDumbbotOrders(DumbbotRequestDto $request): DumbbotResponseDto
    {
        $response = Http::baseUrl(config('diplomacy.adjudicator.base_url'))->post('dumbbot', $request->toArray());
        $response->throw();

        $dto = new DumbbotResponseDto($response->json());
        $dto->json = $response->body();

        return $dto;
    }

    protected function do(mixed $data, callable $callback): string
    {
        $hash = hash('sha256', json_encode($data));
        $location = base_path('tests/cache/'.$hash.'.response.json');

        if (file_exists($location)) {
            $json_raw = file_get_contents($location);
        } else {
            $json_raw = $callback($data);
            file_put_contents($location, $json_raw);
        }

        return $json_raw;
    }
}
