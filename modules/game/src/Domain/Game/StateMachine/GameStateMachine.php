<?php

namespace Dnw\Game\Domain\Game\StateMachine;

class GameStateMachine
{
    /**
     * @var array<string, array<string>>
     */
    private const array ALLOWED_TRANSITIONS = [
        GameStates::CREATED => [GameStates::PLAYERS_JOINING],
        GameStates::PLAYERS_JOINING => [GameStates::ABANDONED, GameStates::ORDER_SUBMISSION, GameStates::NOT_ENOUGH_PLAYERS_BY_DEADLINE],
        GameStates::ORDER_SUBMISSION => [GameStates::ADJUDICATING],
        GameStates::ADJUDICATING => [GameStates::FINISHED, GameStates::ORDER_SUBMISSION],
    ];

    public function __construct(
        private string $currentState,
    ) {}

    public static function initialize(): self
    {
        return new self(GameStates::CREATED);
    }

    public function transitionTo(string $state): void
    {
        $allowedTransition = self::ALLOWED_TRANSITIONS[$this->currentState] ?? [];

        if (! in_array($state, $allowedTransition)) {
            throw new InvalidTransitionException($this->currentState, $state);
        }
        $this->currentState = $state;
    }

    public function hasCurrentState(string $state): bool
    {
        return $this->currentState === $state;
    }

    public function currentStateIsNot(string $state): bool
    {
        return ! $this->hasCurrentState($state);
    }

    public function currentState(): string
    {
        return $this->currentState;
    }
}
