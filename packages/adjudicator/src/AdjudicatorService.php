<?php

namespace Dnw\Adjudicator;

use Dnw\Adjudicator\Dto\AdjudicateGameRequestDto;
use Dnw\Adjudicator\Dto\AdjudicateGameResponseDto;
use Dnw\Adjudicator\Dto\DumbbotRequestDto;
use Dnw\Adjudicator\Dto\DumbbotResponseDto;
use Dnw\Adjudicator\Dto\VariantsResponseDto;

interface AdjudicatorService
{
    public function getVariants(): VariantsResponseDto;

    public function initializeGame(string $variant): AdjudicateGameResponseDto;

    public function adjudicateGame(AdjudicateGameRequestDto $request): AdjudicateGameResponseDto;

    public function getDumbbotOrders(DumbbotRequestDto $request): DumbbotResponseDto;
}
