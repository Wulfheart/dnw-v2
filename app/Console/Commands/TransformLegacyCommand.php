<?php

namespace App\Console\Commands;

use Dnw\Adjudicator\AdjudicatorService;
use Dnw\Adjudicator\Dto\AdjudicateGameRequest;
use Dnw\Adjudicator\Dto\Order;
use Dnw\Legacy\Transform\ResultData\Power;
use Dnw\Legacy\Transform\WebDipOrderTransformer;
use Illuminate\Console\Command;

class TransformLegacyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:transform {id}';

    /**
     * Execute the console command.
     */
    public function handle(
        AdjudicatorService $adjudicator,
    ): void {
        $id = $this->argument('id');
        if (file_exists(__DIR__ . "/data/$id")) {
            exec('rm -rf ' . __DIR__ . "/data/$id");
        }
        mkdir(__DIR__ . "/data/$id");
        $this->info("Starting to transform game {$this->argument('id')}");
        $result = WebDipOrderTransformer::build()->transformGameById((int) $this->argument('id'));
        $this->info('Game transformed, initializing...');

        $initial = $adjudicator->initializeGame('standard');
        $this->info("Initialized to {$initial->phase_long}, starting to adjudicate...");
        $previousStateEncoded = $initial->current_state_encoded;
        foreach ($result->turns as $turn) {
            $orderDtos = collect($turn->powers)->map(
                fn (Power $p) => new Order($p->name, $p->orders)
            )->toArray();
            $request = new AdjudicateGameRequest(
                $previousStateEncoded,
                $orderDtos,
                18
            );

            $combination = "{$id}_{$turn->index}";
            $this->line("Adjudicating {$turn->index}...");
            file_put_contents(__DIR__ . "/data/$id/{$combination}_request.json", json_encode($request));
            $response = $adjudicator->adjudicateGame($request);
            file_put_contents(__DIR__ . "/data/$id/{$combination}_svg_with_orders.svg", $response->svg_with_orders);
            file_put_contents(__DIR__ . "/data/$id/{$combination}_svg_adjudicated.svg", $response->svg_adjudicated);

            $debugData = [
                'orders' => $request->orders,
                'applied_orders' => $response->applied_orders,
            ];
            file_put_contents(__DIR__ . "/data/$id/{$combination}_debug_data.json", json_encode($debugData, JSON_PRETTY_PRINT));
            $this->info("Adjudicated {$turn->index} to {$response->phase_long}");

            if ($response->winners) {
                $this->info('Game over!');
                $this->info(implode(', ', $response->winners));
                break;
            }
            $previousStateEncoded = $response->current_state_encoded;

        }

    }
}
