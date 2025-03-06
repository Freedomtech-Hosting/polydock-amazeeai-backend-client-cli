<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use FreedomtechHosting\PolydockAmazeeAIBackendClient\Client;
use FreedomtechHosting\PolydockAmazeeAIBackendClient\Exception\HttpException;


class UserLoginCommand extends AmazeeAIBaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:login {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Login to the amazee.io AI backend';

    /**
     * Execute the console command.
     */
    public function handle()
    {
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

        $this->initializeClient();

        try {
            $response = $this->client->login($email, $password);
            if (isset($response['access_token']) && isset($response['token_type'])) {
                $this->table(
                    ['access_token', 'token_type'],
                    [[
                        $response['access_token'],
                        $response['token_type']
                    ]]
                );

            } else {
                $this->error('Invalid response from server');
                $this->table(
                    array_keys($response),
                    [array_values($response)]
                );
            }
        } catch (HttpException $e) {
            $this->error(sprintf(
                'HTTP Error %d: %s',
                $e->getStatusCode(),
                json_encode($e->getResponse(), JSON_PRETTY_PRINT)
            ));
        }

    }
}
