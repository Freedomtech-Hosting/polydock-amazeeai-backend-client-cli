<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use FreedomtechHosting\PolydockAmazeeAIBackendClient\Client;


class HealthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'health';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call the API Health Endpoint';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = app(Client::class, ["token" => "1234567890"]);
        $response = $client->health();

        if (is_array($response) && isset($response['status'])) {
            if ($response['status'] === 'healthy') {
                $this->info('healthy');
            } else {
                $this->error($response['status']);
            }
        } else {
            $this->error(print_r($response, true));
        }
    }
}
