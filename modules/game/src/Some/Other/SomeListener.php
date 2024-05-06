<?php

namespace Dnw\Game\Some\Other;


use Dnw\Foundation\Event\Attributes\DomainListener;

#[DomainListener]
final class SomeListener
{
    public function handle(\Dnw\Game\Events\FooEvent $event): void
    {
        log("Handling FooEvent in SomeListener");
    }
}
