<?php

namespace Tests;

use App\Foundation\Id\IdGeneratorInterface;
use Dnw\Foundation\Identity\Id;

trait FakeIdGenerator
{
    protected function useFakeIdGenerator(): void
    {
        app()->singleton(IdGeneratorInterface::class, \App\Foundation\Id\FakeIdGenerator::class);
    }

    protected function addFakeId(Id $id): void
    {
        /** @var \App\Foundation\Id\FakeIdGenerator $idGenerator */
        $idGenerator = app(IdGeneratorInterface::class);

        $idGenerator->addId($id);
    }
}
