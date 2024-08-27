<?php

namespace Dnw\Foundation\Event;

interface ClassFinderInterface
{
    /**
     * @return array<string>
     */
    public function getClassesInPathRecursively(string $path): array;
}
