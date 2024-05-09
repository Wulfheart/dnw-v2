<?php

namespace Dnw\Foundation\Bus;

interface BusInterface {
    public function handle(mixed $command): mixed;
}
