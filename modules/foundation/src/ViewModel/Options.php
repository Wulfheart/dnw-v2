<?php

namespace Dnw\Foundation\ViewModel;

class Options extends ViewModel {
    /**
     * @param array<Option> $options
     */
    public function __construct(
        public array $options = []
    )
    {
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

    /**
     * @return array<string, string>
     */
    public function getFilamentArray(): array
    {
        $result = [];
        foreach ($this->options as $option) {
            $result[$option->value] = $option->label;
        }

        return $result;
    }
}
