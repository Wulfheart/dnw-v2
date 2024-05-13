<?php

namespace Dnw\Adjudicator\Json;

use JsonSerializable;

class JsonHandler implements JsonHandlerInterface
{
    public function decodeAssociative(string $json): array
    {
        $decoded = json_decode($json, true);
        if ($decoded === false) {
            throw new JsonException('Failed to decode JSON ' . json_last_error_msg());
        }

        return $decoded;
    }

    public function encode(JsonSerializable $jsonSerializable): string
    {
        $encoded = json_encode($jsonSerializable);
        if ($encoded === false) {
            throw new JsonException('Failed to encode JSON ' . json_last_error_msg());
        }

        return $encoded;
    }
}
