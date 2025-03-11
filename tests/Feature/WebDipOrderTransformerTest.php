<?php

namespace Tests\Feature;

use Dnw\Legacy\Transform\WebDipOrderTransformer;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\LaravelTestCase;

#[CoversClass(WebDipOrderTransformer::class)]
class WebDipOrderTransformerTest extends LaravelTestCase
{
    public function test(): void
    {
        $result = WebDipOrderTransformer::build()->transformGameById(27588);

        file_put_contents(__DIR__ . '/foo.json', json_encode($result, JSON_PRETTY_PRINT));
        $this->expectNotToPerformAssertions();
    }
}
