<?php

namespace Dnw\Adjudicator;

use Dnw\Adjudicator\Dto\AdjudicateGameRequest;
use Dnw\Adjudicator\Dto\AdjudicateGameResponse;
use Dnw\Adjudicator\Dto\DumbbotRequest;
use Dnw\Adjudicator\Dto\DumbbotResponse;
use Dnw\Adjudicator\Dto\VariantsResponse;
use Exception;

class FakeAdjudicatorService implements AdjudicatorService
{
    public function __construct(
        /** @var ?array<string, AdjudicateGameResponse> $initializeGameResponses */
        private ?array $initializeGameResponses = null,
        /** @var ?array<string, AdjudicateGameResponse> $adjudicateGameResponses */
        private ?array $adjudicateGameResponses = null,
        private ?VariantsResponse $variants = null,
    ) {

    }

    public function getVariants(): VariantsResponse
    {
        if ($this->variants === null) {
            throw new Exception('Variants not set');
        }

        return $this->variants;
    }

    public function initializeGame(string $variant): AdjudicateGameResponse
    {
        if ($this->initializeGameResponses === null) {
            throw new Exception('Initialize games not set');
        }

        if (! array_key_exists($variant, $this->initializeGameResponses)) {
            throw new Exception('Variant not found');
        }

        return $this->initializeGameResponses[$variant];
    }

    public function adjudicateGame(AdjudicateGameRequest $request): AdjudicateGameResponse
    {
        if ($this->adjudicateGameResponses === null) {
            throw new Exception('Adjudicate games not set');
        }

        if (! array_key_exists($request->previous_state_encoded, $this->adjudicateGameResponses)) {
            throw new Exception('Game not found');
        }

        return $this->adjudicateGameResponses[$request->previous_state_encoded];
    }

    public function getDumbbotOrders(DumbbotRequest $request): DumbbotResponse
    {
        throw new Exception('Not implemented');
    }
}
