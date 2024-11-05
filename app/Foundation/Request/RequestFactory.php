<?php

namespace App\Foundation\Request;

abstract class RequestFactory // extends \Worksome\RequestFactories\RequestFactory
{
    final public function __construct(
        /** @var array<string, mixed> $data */
        private array $data = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    abstract protected static function definition(): array;

    public static function new(): static
    {
        return new static(static::definition());
    }

    public function override(string $key, mixed $value): static
    {
        return $this->state([$key => $value]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function state(array $data): static
    {
        $newData = array_merge($this->data, $data);

        return new static($newData);
    }

    public function without(string $key): static
    {
        $newData = [];
        foreach ($this->data as $dataKey => $value) {
            if ($key !== $dataKey) {
                $newData[$dataKey] = $value;
            }
        }

        return new static($newData);
    }

    /**
     * @return array<mixed>
     */
    public function create(): array
    {
        return $this->data;
    }
}
