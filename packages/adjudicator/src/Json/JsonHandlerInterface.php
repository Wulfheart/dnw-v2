<?php

namespace Dnw\Adjudicator\Json;

use JsonSerializable;

interface JsonHandlerInterface
{
    /**
     * @throws JsonException
     *
     * @return array<mixed>
     */
    public function decodeAssociative(string $json): array;

    /**
     * @throws JsonException
     */
    public function encode(JsonSerializable $jsonSerializable): string;
}
