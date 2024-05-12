<?php

namespace Dnw\Adjudicator;

use Dnw\Adjudicator\Dto\AdjudicateGameRequest;
use Dnw\Adjudicator\Dto\AdjudicateGameResponse;
use Dnw\Adjudicator\Dto\DumbbotRequest;
use Dnw\Adjudicator\Dto\DumbbotResponse;
use Dnw\Adjudicator\Dto\VariantsResponse;

interface AdjudicatorService
{
    public function getVariants(): VariantsResponse;

    public function initializeGame(string $variant): AdjudicateGameResponse;

    public function adjudicateGame(AdjudicateGameRequest $request): AdjudicateGameResponse;

    public function getDumbbotOrders(DumbbotRequest $request): DumbbotResponse;
}
