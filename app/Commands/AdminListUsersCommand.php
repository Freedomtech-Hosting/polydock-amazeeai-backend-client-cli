<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use FreedomtechHosting\PolydockAmazeeAIBackendClient\Client;
use FreedomtechHosting\PolydockAmazeeAIBackendClient\Exception\HttpException;


class AdminListUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:list-users {--token=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users';

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
            $response = $client->listUsers();
            $this->table(
                ['email', 'id', 'is_active', 'is_admin'],
                array_map(function($user) {
                    return [
                        $user['email'],
                        $user['id'],
                        $user['is_active'] ? 'Yes' : 'No', 
                        $user['is_admin'] ? 'Yes' : 'No'
                    ];
                }, $response)
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
