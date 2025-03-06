<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use FreedomtechHosting\PolydockAmazeeAIBackendClient\Client;
use FreedomtechHosting\PolydockAmazeeAIBackendClient\Exception\HttpException;


class MeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'me {--token=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find out about the token holder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if($this->option('token')) {
            $this->info('Using token from command line');
            $token = $this->option('token');
        } else {
            $this->info('Using token from environment variable');
            $token = env('POLYDOCK_AMAZEEAI_ADMIN_TOKEN');
        }

        if(!$token) {
            $this->error('No token provided');
            return;
        }


        $client = app(Client::class, ["token" => $token]);
        try {
            $response = $client->getMe();
            $this->table(
                array_keys($response),
                [array_values($response)]
            );
        } catch (HttpException $e) {
            $this->error(sprintf(
                'HTTP Error %d: %s',
                $e->getStatusCode(),
                json_encode($e->getResponse(), JSON_PRETTY_PRINT)
            ));
        }

    }
}
