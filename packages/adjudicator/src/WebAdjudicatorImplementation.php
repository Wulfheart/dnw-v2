<?php

namespace Dnw\Adjudicator;

use Dnw\Adjudicator\Dto\AdjudicateGameRequest;
use Dnw\Adjudicator\Dto\AdjudicateGameResponse;
use Dnw\Adjudicator\Dto\DumbbotRequest;
use Dnw\Adjudicator\Dto\DumbbotResponse;
use Dnw\Adjudicator\Dto\VariantsResponse;
use Dnw\Adjudicator\Json\JsonHandlerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

readonly class WebAdjudicatorImplementation implements AdjudicatorService
{
    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        private Uri $baseUrl,
        private JsonHandlerInterface $jsonHandler,
    ) {}

    public function getVariants(): VariantsResponse
    {
        $path = $this->baseUrl->appendToPath('variants');
        $response = $this->client->sendRequest(
            $this->requestFactory->createRequest(
                'GET',
                (string) $path
            )
        );

        if ($response->getStatusCode() !== 200) {
            throw new HttpException("Failed to get variants at {$path}", $response->getStatusCode());
        }

        $data = $this->jsonHandler->decodeAssociative((string) $response->getBody());

        return VariantsResponse::fromArray($data);
    }

    public function initializeGame(string $variant): AdjudicateGameResponse
    {
        $path = $this->baseUrl->appendToPath('adjudicate/' . $variant);
        $response = $this->client->sendRequest(
            $this->requestFactory->createRequest(
                'GET',
                (string) $path
            )
        );

        if ($response->getStatusCode() !== 200) {
            throw new HttpException("Failed to initialize game at {$path}", $response->getStatusCode());
        }

        $data = $this->jsonHandler->decodeAssociative((string) $response->getBody());

        return AdjudicateGameResponse::fromArray($data);
    }

    public function adjudicateGame(AdjudicateGameRequest $request): AdjudicateGameResponse
    {
        $body = $this->jsonHandler->encode($request);

        $response = $this->client->sendRequest(
            $this->requestFactory->createRequest(
                'POST',
                (string) $this->baseUrl->appendToPath('adjudicate')
            )->withBody(
                $this->streamFactory->createStream($body)
            )->withHeader('Content-Type', 'application/json')
        );

        if ($response->getStatusCode() !== 200) {
            throw new HttpException('Failed to adjudicate game', $response->getStatusCode());
        }

        $data = $this->jsonHandler->decodeAssociative((string) $response->getBody());

        return AdjudicateGameResponse::fromArray($data);
    }

    public function getDumbbotOrders(DumbbotRequest $request): DumbbotResponse
    {
        $response = $this->client->sendRequest(
            $this->requestFactory->createRequest(
                'POST',
                (string) $this->baseUrl->appendToPath('dumbbot')
            )->withBody(
                $this->streamFactory->createStream($this->jsonHandler->encode($request))
            )
        );

        if ($response->getStatusCode() !== 200) {
            throw new HttpException('Failed to get dumbbot orders', $response->getStatusCode());
        }

        $data = $this->jsonHandler->decodeAssociative((string) $response->getBody());

        return DumbbotResponse::fromArray($data);
    }
}
