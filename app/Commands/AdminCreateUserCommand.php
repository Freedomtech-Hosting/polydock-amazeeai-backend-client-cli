<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use FreedomtechHosting\PolydockAmazeeAIBackendClient\Client;
use FreedomtechHosting\PolydockAmazeeAIBackendClient\Exception\HttpException;


class AdminCreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-user {email} {password} {--token=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user';

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

        $email = $this->argument('email');
        if(!$email) {
            $this->error('No email provided');
            return;
        }   

        $password = $this->argument('password');
        if(!$password) {
            $this->error('No password provided');
            return;
        }

        $client = app(Client::class, ["token" => $token]);
        try {
            $response = $client->searchUsers($email);
            if (sizeof($response) > 0) {
                $this->error('User already exists');
                return;
            }
            
            $response = $client->createUser($email, $password);
            if (!isset($response['id']) || $response['email'] !== $email) {
                $this->error('Invalid response from server');
                return;
            }
            $this->table(
                ['email', 'id', 'is_active', 'is_admin'],
                [[
                    $response['email'],
                    $response['id'],
                    $response['is_active'] ? 'Yes' : 'No',
                    $response['is_admin'] ? 'Yes' : 'No'
                ]]
            );

            $this->info('User created successfully');
        } catch (HttpException $e) {
            $this->error(sprintf(
                'HTTP Error %d: %s',
                $e->getStatusCode(),
                json_encode($e->getResponse(), JSON_PRETTY_PRINT)
            ));
        }

    }
}
