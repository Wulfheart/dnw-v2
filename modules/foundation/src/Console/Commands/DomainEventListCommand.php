<?php

namespace Dnw\Foundation\Console\Commands;

use Dnw\Foundation\Event\DomainEventProvider;
use Dnw\Foundation\Event\ListenerInfo;
use Illuminate\Console\Command;

class DomainEventListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'foundation:event:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "List the domain's events and listeners";

    /**
     * Execute the console command.
     */
    public function handle(
        DomainEventProvider $domainEventProvider
    ): void {
        $events = collect($domainEventProvider->getEvents())->sortKeys();

        if ($events->isEmpty()) {
            $this->components->info("Your application doesn't have any events matching the given criteria.");

            return;
        }

        $this->newLine();

        $events->each(function (array $listenerInfos, string $event) {
            /** @var array<ListenerInfo> $listenerInfos */
            $listenerInfos = collect($listenerInfos)->sortBy('class')->toArray();

            $this->components->twoColumnDetail($event);
            $listeners = [];
            foreach ($listenerInfos as $listenerInfo) {
                $listenerString = $listenerInfo->class.'@'.$listenerInfo->method;
                if ($listenerInfo->isAsync) {
                    $listenerString .= ' <fg=bright-blue>(Async)</>';
                }
                $listeners[] = $listenerString;
            }
            $this->components->bulletList($listeners);
        });
    }
}
