<?php

namespace Dnw\Game\Http\Controllers\CreateGame\ViewModel;

use Dnw\Foundation\ViewModel\ViewModel;

class VariantInformationOptions extends ViewModel {

    public function __construct(
        /** @var array<VariantInformationOption> $options */
        public array $options = []
    )
    {
    }

    /**
     * @return array<string, string>
     */
    public function getFilamentArray(): array
    {
        $result = [];
        foreach ($this->options as $option) {
            $result[$option->value] = $option->name;
        }

        return $result;
    }

    public function getSelectedValue(): ?string
    {
        foreach ($this->options as $option) {
            if ($option->selected) {
                return $option->value;
            }
        }

        return null;
    }

    public function getVariantInformationOption(string $value): VariantInformationOption
    {
        foreach ($this->options as $option) {
            if ($option->value === $value) {
                return $option;
            }
        }

        throw new \InvalidArgumentException("No option with value $value found");
    }
}
