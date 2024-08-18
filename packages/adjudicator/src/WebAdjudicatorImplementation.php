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
use Psr\Http\Message\UriInterface;

readonly class WebAdjudicatorImplementation implements AdjudicatorService
{
    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory,
        private UriInterface $baseUrl,
        private JsonHandlerInterface $jsonHandler,
    ) {}

    public function getVariants(): VariantsResponse
    {
        $response = $this->client->sendRequest(
            $this->requestFactory->createRequest(
                'GET',
                $this->baseUrl->withPath('variants')
            )
        );

        if ($response->getStatusCode() !== 200) {
            throw new HttpException('Failed to get variants', $response->getStatusCode());
        }

        $data = $this->jsonHandler->decodeAssociative((string) $response->getBody());

        return VariantsResponse::fromArray($data);
    }

    public function initializeGame(string $variant): AdjudicateGameResponse
    {
        $response = $this->client->sendRequest(
            $this->requestFactory->createRequest(
                'GET',
                $this->baseUrl->withPath('adjudicate/' . $variant)
            )
        );

        if ($response->getStatusCode() !== 200) {
            throw new HttpException('Failed to initialize game', $response->getStatusCode());
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
                $this->baseUrl->withPath('adjudicate')
            )->withBody(
                $this->streamFactory->createStream($body)
            )
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
                $this->baseUrl->withPath('dumbbot')
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
