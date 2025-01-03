<?php

namespace Dnw\Foundation\Bus;

use Dnw\Foundation\Bus\Interface\Command;
use Dnw\Foundation\Bus\Interface\Query;

interface BusInterface
{
    /**
     * @template T
     *
     * @param  Query<T>|Command<T>  $command
     * @return T
     */
    public function handle(Query|Command $command): mixed;
}
