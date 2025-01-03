<?php

namespace Dnw\Game\Domain\Game\StateMachine;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(GameStateMachine::class)]
class GameStateMachineTest extends TestCase
{
    #[DataProvider('allowedTransitionsProvider')]
    public function test_allowed_transitions(string $base, string $next): void
    {
        $machine = new GameStateMachine($base);
        $machine->transitionTo($next);

        $this->assertTrue($machine->hasCurrentState($next));
        $this->assertFalse($machine->hasCurrentState($base));
        $this->assertTrue($machine->currentStateIsNot($base));
        $this->assertEquals($next, $machine->currentState());
    }

    /**
     * @return array<mixed>
     */
    public static function allowedTransitionsProvider(): array
    {
        $transitions = [
            [GameStates::CREATED, GameStates::PLAYERS_JOINING],
            [GameStates::PLAYERS_JOINING, GameStates::ABANDONED],
            [GameStates::PLAYERS_JOINING, GameStates::ORDER_SUBMISSION],
            [GameStates::PLAYERS_JOINING, GameStates::NOT_ENOUGH_PLAYERS_BY_DEADLINE],
            [GameStates::ORDER_SUBMISSION, GameStates::ADJUDICATING],
            [GameStates::ADJUDICATING, GameStates::FINISHED],
            [GameStates::ADJUDICATING, GameStates::ORDER_SUBMISSION],
        ];

        $transformedData = [];
        foreach ($transitions as $transition) {
            $key = implode(' -> ', $transition);
            $transformedData[$key] = $transition;
        }

        return $transformedData;
    }

    #[DataProvider('disallowedTransitionsProvider')]
    public function test_disallowed_transitions(string $base, string $next): void
    {
        $machine = new GameStateMachine($base);
        $this->expectException(InvalidTransitionException::class);
        $machine->transitionTo($next);

    }

    /**
     * @return array<mixed>
     */
    public static function disallowedTransitionsProvider(): array
    {
        $transitions = [
            [GameStates::CREATED, GameStates::CREATED],
            [GameStates::CREATED, GameStates::ABANDONED],
            [GameStates::CREATED, GameStates::ORDER_SUBMISSION],
            [GameStates::CREATED, GameStates::ADJUDICATING],
            [GameStates::CREATED, GameStates::NOT_ENOUGH_PLAYERS_BY_DEADLINE],
            [GameStates::PLAYERS_JOINING, GameStates::CREATED],
            [GameStates::PLAYERS_JOINING, GameStates::PLAYERS_JOINING],
            [GameStates::PLAYERS_JOINING, GameStates::FINISHED],
            [GameStates::PLAYERS_JOINING, GameStates::ADJUDICATING],
            [GameStates::ABANDONED, GameStates::CREATED],
            [GameStates::ABANDONED, GameStates::PLAYERS_JOINING],
            [GameStates::ABANDONED, GameStates::ORDER_SUBMISSION],
            [GameStates::ABANDONED, GameStates::ADJUDICATING],
            [GameStates::ABANDONED, GameStates::FINISHED],
            [GameStates::ABANDONED, GameStates::NOT_ENOUGH_PLAYERS_BY_DEADLINE],
            [GameStates::ORDER_SUBMISSION, GameStates::CREATED],
            [GameStates::ORDER_SUBMISSION, GameStates::PLAYERS_JOINING],
            [GameStates::ORDER_SUBMISSION, GameStates::ABANDONED],
            [GameStates::ORDER_SUBMISSION, GameStates::ORDER_SUBMISSION],
            [GameStates::ORDER_SUBMISSION, GameStates::FINISHED],
            [GameStates::ORDER_SUBMISSION, GameStates::NOT_ENOUGH_PLAYERS_BY_DEADLINE],
            [GameStates::ADJUDICATING, GameStates::CREATED],
            [GameStates::ADJUDICATING, GameStates::PLAYERS_JOINING],
            [GameStates::ADJUDICATING, GameStates::ABANDONED],
            [GameStates::ADJUDICATING, GameStates::ADJUDICATING],
            [GameStates::ADJUDICATING, GameStates::NOT_ENOUGH_PLAYERS_BY_DEADLINE],
            [GameStates::FINISHED, GameStates::CREATED],
            [GameStates::FINISHED, GameStates::PLAYERS_JOINING],
            [GameStates::FINISHED, GameStates::ABANDONED],
            [GameStates::FINISHED, GameStates::ORDER_SUBMISSION],
            [GameStates::FINISHED, GameStates::ADJUDICATING],
            [GameStates::FINISHED, GameStates::FINISHED],
            [GameStates::FINISHED, GameStates::NOT_ENOUGH_PLAYERS_BY_DEADLINE],
            [GameStates::NOT_ENOUGH_PLAYERS_BY_DEADLINE, GameStates::CREATED],
            [GameStates::NOT_ENOUGH_PLAYERS_BY_DEADLINE, GameStates::PLAYERS_JOINING],
            [GameStates::NOT_ENOUGH_PLAYERS_BY_DEADLINE, GameStates::ABANDONED],
            [GameStates::NOT_ENOUGH_PLAYERS_BY_DEADLINE, GameStates::ORDER_SUBMISSION],
            [GameStates::NOT_ENOUGH_PLAYERS_BY_DEADLINE, GameStates::ADJUDICATING],
            [GameStates::NOT_ENOUGH_PLAYERS_BY_DEADLINE, GameStates::FINISHED],
            [GameStates::NOT_ENOUGH_PLAYERS_BY_DEADLINE, GameStates::NOT_ENOUGH_PLAYERS_BY_DEADLINE],

        ];

        $transformedData = [];
        foreach ($transitions as $transition) {
            $key = implode(' -> ', $transition);
            $transformedData[$key] = $transition;
        }

        return $transformedData;
    }

    public function test_initialize(): void
    {
        $machine = GameStateMachine::initialize();

        $this->assertTrue($machine->hasCurrentState(GameStates::CREATED));
    }
}
