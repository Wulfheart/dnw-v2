<?php

namespace Dnw\Game\Test\Feature\Fake;

use Dnw\Adjudicator\WebAdjudicatorImplementation;
use GuzzleHttp\Client;
use Psr\Http\Client\ClientInterface;

trait FakeWebDipAdjudicatorImplementation
{
    protected function fakeWebDipAdjudicatorImplementation(string $fixturePath): void
    {
        $fakeClient = new FakeClient(
            app()->make(Client::class),
            $fixturePath,
        );

        app()->when(WebAdjudicatorImplementation::class)
            ->needs(ClientInterface::class)
            ->give(fn () => $fakeClient);
    }
}
