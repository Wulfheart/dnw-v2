<?php

namespace Dnw\Game\Application\Test;

use Dnw\Foundation\Bus\Interface\Command;
use Dnw\Foundation\Collection\ArrayCollection;
use Dnw\Foundation\Identity\Id;
use Dnw\Game\Application\Command\CreateGame\CreateGameCommand;
use Dnw\Game\Application\Command\CreateVariant\CreateVariantCommand;
use Dnw\Game\Application\Command\CreateVariant\VariantPowerInfo;
use Dnw\Game\Application\Command\InitialGameAdjudication\InitialGameAdjudicationCommand;
use Dnw\Game\Test\Feature\Fake\FakeName\FakeGameNameProvider;
use Wulfheart\Option\Option;

final class GameCommandBuilder
{
    /** @var ArrayCollection<Command<mixed>> */
    private ArrayCollection $commands;

    private function __construct(
        private Id $gameId,
        string $gameName,
        Id $creatorId,
    ) {
        $this->commands = new ArrayCollection();

        $variantCommand = new CreateVariantCommand(
            'standard',
            'Standard',
            'Standard',
            18,
            36,
            ArrayCollection::build(
                new VariantPowerInfo('AUSTRIA', 'Austria', 'red'),
                new VariantPowerInfo('ENGLAND', 'Great Britain', 'pink'),
                new VariantPowerInfo('GERMANY', 'Germany', 'brown'),
                new VariantPowerInfo('RUSSIA', 'Russia', 'violet'),
                new VariantPowerInfo('ITALY', 'Italy', 'green'),
                new VariantPowerInfo('FRANCE', 'France', 'blue'),
                new VariantPowerInfo('TURKEY', 'Turkey', 'yellow'),
            )
        );

        $this->commands->push($variantCommand);

        $c = new CreateGameCommand(
            $gameId,
            $gameName,
            60,
            4,
            true,
            'standard',
            true,
            Option::none(),
            true,
            false,
            [],
            $creatorId
        );

        $this->commands->push($c);

    }

    public static function initialize(): self
    {
        return new self(
            Id::generate(),
            FakeGameNameProvider::name(),
            Id::generate(),
        );
    }

    public function storeInitialAdjudication(): self
    {
        $command = new InitialGameAdjudicationCommand($this->gameId);
        $this->commands->push($command);

        return $this;
    }

    /**
     * @return ArrayCollection<Command<mixed>>
     */
    public function build(): ArrayCollection
    {
        return $this->commands;
    }
}
