<?php

namespace Dnw\Game\Application\Query\GetGameById;

use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\DateTime\DateTime;
use Dnw\Game\Application\Query\GetGameById\Dto\PhaseDto;
use Dnw\Game\Application\Query\GetGameById\Dto\PhasesInDescendingOrderDto;
use Dnw\Game\Application\Query\GetGameById\Dto\VariantPowerDataDto;
use Dnw\Game\Application\Query\Shared\Game\GameInfo\GameStateEnum;
use Dnw\Game\Domain\Adapter\TimeProvider\TimeProviderInterface;
use Dnw\Game\Domain\Game\Entity\Power;
use Dnw\Game\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Domain\Game\Repository\Phase\Impl\Laravel\PhaseModel;
use Dnw\Game\Domain\Game\Repository\Phase\PhaseRepositoryInterface;
use Dnw\Game\Domain\Game\ValueObject\Game\GameId;
use Dnw\Game\Domain\Game\ValueObject\Phase\PhaseId;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use Dnw\Game\Domain\Variant\Repository\VariantRepositoryInterface;
use Exception;
use Psr\Log\LoggerInterface;

readonly class GetGameByIdQueryHandler
{
    public function __construct(
        private GameRepositoryInterface $gameRepository,
        private VariantRepositoryInterface $variantRepository,
        private TimeProviderInterface $timeProvider,
        private PhaseRepositoryInterface $phaseRepository,
        private LoggerInterface $logger
    ) {}

    public function handle(GetGameByIdQuery $query): GetGameByIdQueryResult
    {
        $gameId = GameId::fromId($query->id);
        $gameResult = $this->gameRepository->load($gameId);
        if ($gameResult->isErr()) {
            return GetGameByIdQueryResult::err(GetGameByIdQueryResult::E_GAME_NOT_FOUND);
        }
        $game = $gameResult->unwrap();

        $variantResult = $this->variantRepository->load($game->variant->id);
        if ($variantResult->isErr()) {
            // ERROR: There should be no occurrence of a non-existent variant when a game is found
            $this->logger->error('Variant not found for game', [
                'gameId' => $query->id,
                'variantId' => $game->variant->id,
            ]);
            throw new Exception("Variant {$game->variant->id} not found for game {$game->gameId}");
        }
        $variant = $variantResult->unwrap();

        return GetGameByIdQueryResult::ok(
            new GetGameByIdQueryResultData(
                $query->id,
                GameStateEnum::fromGameState($game->gameStateMachine->currentState()),
                $game->name,
                $variant->name,
                $game->variant->id,
                $game->adjudicationTiming->phaseLength->minutes(),
                $game->gameStartTiming->endOfJoinPhase(),
                new ArrayCollection(
                    $game->powerCollection->map(
                        function (Power $power) use ($variant) {
                            $variantPower = $variant->variantPowerCollection->getByVariantPowerId($power->variantPowerId);

                            return new VariantPowerDataDto(
                                $power->variantPowerId,
                                $power->playerId->mapIntoOption(
                                    fn (PlayerId $id) => $id->toId()
                                ),
                                $variantPower->name,
                                $variantPower->color,
                            );
                        }
                    )->toArray()
                ),
                $this->fetchAllPhasesInDescendingOrder($gameId),
                $game->canBeJoined(PlayerId::fromId($query->actor), $this->timeProvider->getCurrentTime())->passes(),
                $game->canLeave(PlayerId::fromId($query->actor))->passes(),
            )
        );
    }

    private function fetchAllPhasesInDescendingOrder(GameId $gameId): PhasesInDescendingOrderDto
    {
        $phasesInDescendingOrder = new ArrayCollection();

        $phases = PhaseModel::query()
            ->where('game_id', (string) $gameId)
            ->orderBy('ordinal_number', 'desc')
            ->get();

        foreach ($phases as $phase) {
            $phaseId = PhaseId::fromString($phase->id);
            $phasesInDescendingOrder->push(
                new PhaseDto(
                    $phase->name,
                    $phase->type->value,
                    new DateTime($phase->adjudication_time),
                    $this->phaseRepository->loadLinkToSvgWithOrders($phaseId)->toOption(),
                    $this->phaseRepository->loadLinkToAdjudicatedSvg($phaseId)->toOption()
                )
            );
        }

        return new PhasesInDescendingOrderDto($phasesInDescendingOrder->toArray());
    }
}
