<?php

namespace Dnw\Foundation\Console\Commands;

use Dnw\Foundation\Event\DomainEventProvider;
use Illuminate\Console\Command;

class DomainEventCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'foundation:event:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Discover and cache the domain's events and listeners";

    /**
     * Execute the console command.
     */
    public function handle(
        DomainEventProvider $domainEventProvider
    ): void {
        $domainEventProvider->cacheEvents();
        $this->components->info('Domain events cached successfully.');
    }
}
