<?php

namespace Dnw\Foundation\Event;

interface DomainEventProviderInterface
{
    /**
     * @return array<class-string, array<ListenerInfo>>
     */
    public function getEvents(): array;

    public function cacheEvents(): void;

    public function deleteCachedEvents(): void;
}
