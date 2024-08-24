<?php

namespace Dnw\Game\Tests\Unit\Infrastructure\Repository\Player;

use Dnw\Foundation\DateTime\DateTime;
use Dnw\Game\Core\Domain\Game\Game;
use Dnw\Game\Core\Domain\Game\Repository\GameRepositoryInterface;
use Dnw\Game\Core\Domain\Game\StateMachine\GameStates;
use Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming\GameStartTiming;
use Dnw\Game\Core\Domain\Game\ValueObject\GameStartTiming\JoinLength;
use Dnw\Game\Core\Domain\Player\Player;
use Dnw\Game\Core\Domain\Player\ValueObject\PlayerId;
use Dnw\Game\Core\Infrastructure\Repository\Player\LaravelPlayerRepository;
use Dnw\Game\Tests\Factory\GameBuilder;
use Dnw\Game\Tests\Unit\Domain\Player\Repository\AbstractPlayerRepositoryTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LaravelPlayerRepository::class)]
class LaravelPlayerRepositoryTest extends AbstractPlayerRepositoryTestCase
{
    private GameRepositoryInterface $gameRepository;

    protected function buildRepository(): array
    {
        $gameRepo = $this->app->make(GameRepositoryInterface::class);
        $this->gameRepository = $gameRepo;
        $one = PlayerId::new();
        $this->makeCreated($one);
        $this->makePlayersJoining($one);
        $this->makeAbandoned($one);
        $this->makeOrderSubmission($one, 3);
        $this->makeAdjudicating($one, 2);
        $this->makeFinished($one);
        $this->makeNotEnoughPlayersByDeadline($one);

        $two = PlayerId::new();
        $this->makeCreated($two, 2);
        $this->makePlayersJoining($two);
        $this->makeAbandoned($two);
        $this->makeOrderSubmission($two);
        $this->makeAdjudicating($two);
        $this->makeFinished($two, 4);
        $this->makeNotEnoughPlayersByDeadline($two);

        return [
            $this->app->make(LaravelPlayerRepository::class),
            [
                new Player($one, 7),
                new Player($two, 5),
            ],
        ];
    }

    private function makeCreated(PlayerId $playerId, int $count = 1): void
    {
        for ($i = 0; $i < $count; $i++) {
            $game = GameBuilder::initialize(playerId: $playerId)->build();
            $this->assertTrue($game->gameStateMachine->hasCurrentState(GameStates::CREATED));

            $this->persist($game);
        }
    }

    private function makePlayersJoining(PlayerId $playerId, int $count = 1): void
    {
        for ($i = 0; $i < $count; $i++) {
            $game = GameBuilder::initialize()->storeInitialAdjudication()->join($playerId)->build();

            $this->assertTrue($game->gameStateMachine->hasCurrentState(GameStates::PLAYERS_JOINING));
            $this->persist($game);
        }

    }

    private function makeAbandoned(PlayerId $playerId, int $count = 1): void
    {
        for ($i = 0; $i < $count; $i++) {

            $game = GameBuilder::initialize(playerId: $playerId)->storeInitialAdjudication()->abandon()->build();

            $this->assertTrue($game->gameStateMachine->hasCurrentState(GameStates::ABANDONED));
            $this->persist($game);
        }

    }

    private function makeOrderSubmission(PlayerId $playerId, int $count = 1): void
    {
        for ($i = 0; $i < $count; $i++) {
            $game = GameBuilder::initialize()->storeInitialAdjudication()->join($playerId)->makeFull()->build();

            $this->assertTrue($game->gameStateMachine->hasCurrentState(GameStates::ORDER_SUBMISSION));
            $this->persist($game);
        }

    }

    private function makeAdjudicating(PlayerId $playerId, int $count = 1): void
    {
        for ($i = 0; $i < $count; $i++) {
            $game = GameBuilder::initialize()->storeInitialAdjudication()->join($playerId)->makeFull()->transitionToAdjudicating()->build();

            $this->assertTrue($game->gameStateMachine->hasCurrentState(GameStates::ADJUDICATING));
            $this->persist($game);
        }

    }

    private function makeFinished(PlayerId $playerId, int $count = 1): void
    {
        for ($i = 0; $i < $count; $i++) {
            $game = GameBuilder::initialize()->storeInitialAdjudication()->join($playerId)->makeFull()->transitionToAdjudicating()->finish()->build();

            $this->assertTrue($game->gameStateMachine->hasCurrentState(GameStates::FINISHED));
            $this->persist($game);
        }

    }

    private function makeNotEnoughPlayersByDeadline(PlayerId $playerId, int $count = 1): void
    {
        for ($i = 0; $i < $count; $i++) {
            $gameStartTiming = new GameStartTiming(
                new DateTime('2021-01-01 00:00:00'),
                JoinLength::fromDays(3),
                false
            );
            $game = GameBuilder::initialize(gameStartTiming: $gameStartTiming)->storeInitialAdjudication()->build();
            $game->handleGameJoinLengthExceeded(new DateTime('2021-01-05 00:00:00'));

            $this->assertTrue($game->gameStateMachine->hasCurrentState(GameStates::NOT_ENOUGH_PLAYERS_BY_DEADLINE));
            $this->persist($game);
        }

    }

    private function persist(Game $game): void
    {
        $this->gameRepository->save($game);
    }
}
