<?php

namespace Dnw\Game\Domain\Player\Repository\Player;

use Dnw\Foundation\DateTime\DateTime;
use Dnw\Foundation\PHPStan\AllowLaravelTestCase;
use Dnw\Game\Domain\Game\Game;
use Dnw\Game\Domain\Game\Repository\Game\GameRepositoryInterface;
use Dnw\Game\Domain\Game\StateMachine\GameStates;
use Dnw\Game\Domain\Game\Test\Factory\GameBuilder;
use Dnw\Game\Domain\Game\ValueObject\GameStartTiming\GameStartTiming;
use Dnw\Game\Domain\Game\ValueObject\GameStartTiming\JoinLength;
use Dnw\Game\Domain\Player\Player;
use Dnw\Game\Domain\Player\ValueObject\PlayerId;
use Tests\TestCase;

#[AllowLaravelTestCase]
abstract class AbstractPlayerRepositoryTestCase extends TestCase
{
    abstract protected function buildPlayerRepo(): PlayerRepositoryInterface;

    abstract protected function buildGameRepo(): GameRepositoryInterface;

    public function test_load_defaults_to_zero_count(): void
    {
        $repo = $this->buildPlayerRepo();
        $result = $repo->load(PlayerId::new());
        $this->assertEquals(0, $result->unwrap()->numberOfCurrentlyPlayingGames);
    }

    public function test_load(): void
    {
        $gameRepo = $this->buildGameRepo();
        $one = PlayerId::new();
        $this->makeCreated($one, $gameRepo);
        $this->makePlayersJoining($one, $gameRepo);
        $this->makeAbandoned($one, $gameRepo);
        $this->makeOrderSubmission($one, $gameRepo, 3);
        $this->makeAdjudicating($one, $gameRepo, 2);
        $this->makeFinished($one, $gameRepo);
        $this->makeNotEnoughPlayersByDeadline($one, $gameRepo);

        $two = PlayerId::new();
        $this->makeCreated($two, $gameRepo, 2);
        $this->makePlayersJoining($two, $gameRepo);
        $this->makeAbandoned($two, $gameRepo);
        $this->makeOrderSubmission($two, $gameRepo);
        $this->makeAdjudicating($two, $gameRepo);
        $this->makeFinished($two, $gameRepo, 4);
        $this->makeNotEnoughPlayersByDeadline($two, $gameRepo);

        $repo = $this->buildPlayerRepo();
        $players =  [
            new Player($one, 7),
            new Player($two, 5),
        ];
        foreach ($players as $expectedPlayer) {
            $result = $repo->load($expectedPlayer->playerId);
            $this->assertEquals($expectedPlayer, $result->unwrap());
        }
    }

    private function makeCreated(PlayerId $playerId, GameRepositoryInterface $repo, int $count = 1): void
    {
        for ($i = 0; $i < $count; $i++) {
            $game = GameBuilder::initialize(playerId: $playerId)->build();
            $this->assertTrue($game->gameStateMachine->hasCurrentState(GameStates::CREATED));

            $this->persist($game, $repo);
        }
    }

    private function makePlayersJoining(PlayerId $playerId, GameRepositoryInterface $repo, int $count = 1): void
    {
        for ($i = 0; $i < $count; $i++) {
            $game = GameBuilder::initialize()->storeInitialAdjudication()->join($playerId)->build();

            $this->assertTrue($game->gameStateMachine->hasCurrentState(GameStates::PLAYERS_JOINING));
            $this->persist($game, $repo);
        }

    }

    private function makeAbandoned(PlayerId $playerId, GameRepositoryInterface $repo, int $count = 1): void
    {
        for ($i = 0; $i < $count; $i++) {

            $game = GameBuilder::initialize(playerId: $playerId)->storeInitialAdjudication()->abandon()->build();

            $this->assertTrue($game->gameStateMachine->hasCurrentState(GameStates::ABANDONED));
            $this->persist($game, $repo);
        }

    }

    private function makeOrderSubmission(PlayerId $playerId, GameRepositoryInterface $repo, int $count = 1): void
    {
        for ($i = 0; $i < $count; $i++) {
            $game = GameBuilder::initialize()->storeInitialAdjudication()->join($playerId)->makeFull()->build();

            $this->assertTrue($game->gameStateMachine->hasCurrentState(GameStates::ORDER_SUBMISSION));
            $this->persist($game, $repo);
        }

    }

    private function makeAdjudicating(PlayerId $playerId, GameRepositoryInterface $repo, int $count = 1): void
    {
        for ($i = 0; $i < $count; $i++) {
            $game = GameBuilder::initialize()->storeInitialAdjudication()->join($playerId)->makeFull()->transitionToAdjudicating()->build();

            $this->assertTrue($game->gameStateMachine->hasCurrentState(GameStates::ADJUDICATING));
            $this->persist($game, $repo);
        }

    }

    private function makeFinished(PlayerId $playerId, GameRepositoryInterface $repo, int $count = 1): void
    {
        for ($i = 0; $i < $count; $i++) {
            $game = GameBuilder::initialize()->storeInitialAdjudication()->join($playerId)->makeFull()->transitionToAdjudicating()->finish()->build();

            $this->assertTrue($game->gameStateMachine->hasCurrentState(GameStates::FINISHED));
            $this->persist($game, $repo);
        }

    }

    private function makeNotEnoughPlayersByDeadline(PlayerId $playerId, GameRepositoryInterface $repo, int $count = 1): void
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
            $this->persist($game, $repo);
        }

    }

    private function persist(Game $game, GameRepositoryInterface $repo): void
    {
        $repo->save($game);
    }
}
