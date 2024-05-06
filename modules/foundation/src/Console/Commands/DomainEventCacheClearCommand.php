<?php

namespace Dnw\Foundation\Console\Commands;

use Dnw\Foundation\Event\DomainEventProvider;
use Illuminate\Console\Command;

class DomainEventCacheClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'foundation:event:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Clear all cached domain events and listeners";

    /**
     * Execute the console command.
     */
    public function handle(
        DomainEventProvider $domainEventProvider
    ): void
    {
        $domainEventProvider->deleteCachedEvents();
        $this->components->info('Cached domain events cleared successfully.');
    }
}
