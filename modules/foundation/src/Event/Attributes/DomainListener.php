<?php

namespace Dnw\Foundation\Event\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class DomainListener
{
    public function __construct(
        public readonly bool $async = false,
    ) {}
}
